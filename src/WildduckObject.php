<?php

namespace Zone\Wildduck;

use Override;
use ArrayAccess;
use Countable;
use JsonSerializable;
use Zone\Wildduck\Resource\ApiResource;
use Zone\Wildduck\Resource\Message;
use Zone\Wildduck\Util\Set;
use Zone\Wildduck\Util\Util;
use Zone\Wildduck\Util\RequestOptions;
use Zone\Wildduck\Exception\InvalidArgumentException;


/**
 * Class WildduckObject.
 */
class WildduckObject implements ArrayAccess, Countable, JsonSerializable
{
    /** @var RequestOptions */
    protected RequestOptions $_opts;

    /** @var array */
    protected array $_originalValues;

    /** @var array */
    protected array $_values = [];

    /** @var Set */
    protected Set $_unsavedValues;

    /** @var Set */
    protected Set $_transientValues;

    /** @var array|null */
    protected array|null $_retrieveOptions;

    /** @var null|ApiResponse */
    protected ?ApiResponse $_lastResponse = null;

    public string $id;

    /**
     * @return Set Attributes that should not be sent to the API because
     *    they're not updatable (e.g. ID).
     */
    public static function getPermanentAttributes(): Set
    {
        static $permanentAttributes = null;
        if (null === $permanentAttributes) {
            $permanentAttributes = new Set([
                'id',
            ]);
        }

        return $permanentAttributes;
    }

    /**
     * Additive objects are subobjects in the API that don't have the same
     * semantics as most subobjects, which are fully replaced when they're set.
     *
     * This is best illustrated by example. The `source` parameter sent when
     * updating a subscription is *not* additive; if we set it:
     *
     *     source[object]=card&source[number]=123
     *
     * We expect the old `source` object to have been overwritten completely. If
     * the previous source had an `address_state` key associated with it, and we
     * didn't send one this time, that value of `address_state` is gone.
     *
     * By contrast, additive objects are those that will have new data added to
     * them while keeping any existing data in place. The only known case of its
     * use is for `metadata`, but it could in theory be more general. As an
     * example, say we have a `metadata` object that looks like this on the
     * server side:
     *
     *     metadata = ["old" => "old_value"]
     *
     * If we update the object with `metadata[new]=new_value`, the server side
     * object now has *both* fields:
     *
     *     metadata = ["old" => "old_value", "new" => "new_value"]
     *
     * This is okay in itself because usually users will want to treat it as
     * additive:
     *
     *     $obj->metadata["new"] = "new_value";
     *     $obj->save();
     *
     * However, in other cases, they may want to replace the entire existing
     * contents:
     *
     *     $obj->metadata = ["new" => "new_value"];
     *     $obj->save();
     *
     * This is where things get a little bit tricky because in order to clear
     * any old keys that may have existed, we actually have to send an explicit
     * empty string to the server. So the operation above would have to send
     * this form to get the intended behavior:
     *
     *     metadata[old]=&metadata[new]=new_value
     *
     * This method allows us to track which parameters are considered additive,
     * and lets us behave correctly where appropriate when serializing
     * parameters to be sent.
     *
     * @return Set Set of additive parameters
     */
    public static function getAdditiveParams(): Set
    {
        static $additiveParams = null;
        if (null === $additiveParams) {
            // Set `metadata` as additive so that when it's set directly we remember
            // to clear keys that may have been previously set by sending empty
            // values for them.
            //
            // It's possible that not every object has `metadata`, but having this
            // option set when there is no `metadata` field is not harmful.
            $additiveParams = new Set([
                'metadata',
            ]);
        }

        return $additiveParams;
    }

	/**
	 * @param string|array|null $values
	 * @param array|RequestOptions|null $opts
	 */
	public function __construct(string|null|array $values, array|RequestOptions|null $opts = null)
	{
		$id = $values;
		if(is_array($values)) {
			[$id, $this->_retrieveOptions] = Util::normalizeId($values);
		}

		$this->_opts = RequestOptions::parse($opts);
		$this->_originalValues = [];
		$this->_values = [];
		$this->_unsavedValues = new Set();
		$this->_transientValues = new Set();
		if (null !== $values) {
			$this->_values['id'] = $id;
			$this->id = $id;
		}
	}

	/**
	 * This unfortunately needs to be public to be used in Util\Util.
	 *
	 * @param array $values
	 * @param array|RequestOptions|null $opts
	 * @return static the object constructed from the given values
	 */
	public static function constructFrom(array $values, array|RequestOptions|null $opts = null): static
	{
		$obj = new static($values['id'] ?? null);
		$obj->refreshFrom($values, $opts);

		return $obj;
	}

    /**
     * Standard accessor magic methods
     *
     * @param string $name
     * @param string|array|bool $value
     * @return void
     */
    public function __set(mixed $name, string|array|bool $value): void
    {
        if (static::getPermanentAttributes()->includes($name)) {
            throw new InvalidArgumentException(
                sprintf('Cannot set %s on this object. HINT: you can\'t set: ', $name) .
                implode(', ', static::getPermanentAttributes()->toArray())
            );
        }

        if ('' === $value) {
            throw new InvalidArgumentException(
                "You cannot set '" . $name . "'to an empty string. "
                . 'We interpret empty strings as NULL in requests. '
                . 'You may set obj->' . $name . ' = NULL to delete the property'
            );
        }

        $this->_values[$name] = Util::convertToWildduckObject($value, $this->_opts);
        $this->dirtyValue($this->_values[$name]);
        $this->_unsavedValues->add($name);
    }
    public function __isset(mixed $name): bool
    {
        return isset($this->_values[$name]);
    }
    public function __unset(mixed $name): void
    {
        unset($this->_values[$name]);
        $this->_transientValues->add($name);
        $this->_unsavedValues->discard($name);
    }

    /**
     * @param mixed $name
     * @return string|null|array
     */
    public function &__get(mixed $name): mixed
    {
        // function should return a reference, using $nullval to return a reference to null
        $nullval = null;
        if ($this->_values !== [] && array_key_exists($name, $this->_values)) {
            return $this->_values[$name];
        }

	    $class = static::class;
	    if ($this->_transientValues->includes($name)) {
		    $attrs = implode(', ', array_keys($this->_values));
            $message = sprintf('Wildduck Notice: Undefined property of %s instance: %s. ', $class, $name)
                    . sprintf('HINT: The %s attribute was set in the past, however. ', $name)
                    . 'It was then wiped when refreshing the object '
                    . "with the result returned by Wildduck's API, "
                    . 'probably as a result of a save(). The attributes currently '
                    . ('available on this object are: ' . $attrs);
            Wildduck::getLogger()->error($message);

            return $nullval;
        }

	    Wildduck::getLogger()->error(sprintf('Wildduck Notice: Undefined property of %s instance: %s', $class, $name));

        return $nullval;
    }

    // Magic method for var_dump output. Only works with PHP >= 5.6
    public function __debugInfo(): array
    {
        return $this->_values;
    }

    /**
     * ArrayAccess methods
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    #[Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->{$offset} = $value;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    #[Override]
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->_values);
    }

    /**
     * @param mixed $offset
     * @return void
     */
    #[Override]
    public function offsetUnset(mixed $offset): void
    {
        unset($this->{$offset});
    }

    /**
     * @param mixed $offset
     * @return string|null
     */
    #[Override]
    public function offsetGet(mixed $offset): string|null
    {
        return $this->_values[$offset] ?? null;
    }

    /**
     * Countable method
     *
     * @return int
     */
    #[Override]
    public function count(): int
    {
        return count($this->_values);
    }
    public function keys(): array
    {
        return array_keys($this->_values);
    }
    public function values(): array
    {
        return array_values($this->_values);
    }

    /**
     * Refreshes this object using the provided values.
     *
     * @param array|object $values
     * @param array|RequestOptions|string|null $opts
     * @param bool $partial defaults to false
     */
    public function refreshFrom(array|object $values, array|null|RequestOptions|string $opts, bool $partial = false): void
    {
        $this->_opts = RequestOptions::parse($opts);
        $this->_originalValues = self::deepCopy($values);

        if ($values instanceof self) {
            $values = $values->toArray();
        }

        // Wipe old state before setting new.  This is useful for e.g. updating a
        // customer, where there is no persistent card parameter.  Mark those values
        // which don't persist as transient
        $removed = $partial ? new Set() : new Set(array_diff(array_keys($this->_values), array_keys($values)));

        foreach ($removed->toArray() as $k) {
            unset($this->{$k});
        }

        $this->updateAttributes($values, $opts, false);
        foreach (array_keys($values) as $k) {
            $this->_transientValues->discard($k);
            $this->_unsavedValues->discard($k);
        }
    }

    /**
     * Mass assigns attributes on the model.
     *
     * @param array $values
     * @param array|RequestOptions|null $opts
     * @param bool $dirty defaults to true
     */
    public function updateAttributes(array $values, array|RequestOptions|null $opts = null, bool $dirty = true): void
    {
        foreach ($values as $k => $v) {
            // Special-case metadata to always be cast as a WildduckObject
            // This is necessary in case metadata is empty, as PHP arrays do
            // not differentiate between lists and hashes, and we consider
            // empty arrays to be lists.
            if (('metadata' === $k) && (is_array($v))) {
                $this->_values[$k] = self::constructFrom($v, $opts);
            } elseif (is_array($v)) {
                $this->_values[$k] = Util::convertToWildduckObject($v, $opts);
            } else {
                $this->_values[$k] = $v;
            }

            if ($dirty) {
                $this->dirtyValue($this->_values[$k]);
            }

            $this->_unsavedValues->add($k);
        }
    }
    /**
     * @param bool $force defaults to false
     *
     * @return array a recursive mapping of attributes to values for this object,
     *    including the proper value for deleted attributes
     */
    public function serializeParameters(bool $force = false): array
    {
        $updateParams = [];

        foreach ($this->_values as $k => $v) {
            // There are a few reasons that we may want to add in a parameter for
            // update:
            //
            //   1. The `$force` option has been set.
            //   2. We know that it was modified.
            //   3. Its value is a WildduckObject. A WildduckObject may contain modified
            //      values within in that its parent WildduckObject doesn't know about.
            //
            $original = $this->_originalValues[$k] ?? null;
            $unsaved = $this->_unsavedValues->includes($k);
            if ($force || $unsaved || $v instanceof self) {
                $updateParams[$k] = $this->serializeParamsValue(
                    $this->_values[$k],
                    $original,
                    $unsaved,
                    $force,
                    $k
                );
            }
        }

        // a `null` that makes it out of `serializeParamsValue` signals an empty
        // value that we shouldn't appear in the serialized form of the object
        return array_filter(
            $updateParams,
            static fn($v): bool => null !== $v
        );
    }

    /**
     * @param string|object|array|null $value
     * @param string|object|array|null $original
     * @param bool|null $unsaved
     * @param bool $force
     * @param string|null $key
     * @return array|string|null|ApiResource
     */
    public function serializeParamsValue(string|object|array|null $value, string|object|array|null $original, null|bool $unsaved, bool $force, null|string $key = null): array|null|string|ApiResource
    {
        // The logic here is that essentially any object embedded in another
        // object that had a `type` is actually an API resource of a different
        // type that's been included in the response. These other resources must
        // be updated from their proper endpoints, and therefore they are not
        // included when serializing even if they've been modified.
        //
        // There are _some_ known exceptions though.
        //
        // For example, if the value is unsaved (meaning the user has set it), and
        // it looks like the API resource is persisted with an ID, then we include
        // the object so that parameters are serialized with a reference to its
        // ID.
        //
        // Another example is that on save API calls it's sometimes desirable to
        // update a customer's default source by setting a new card (or other)
        // object with `->source=` and then saving the customer. The
        // `saveWithParent` flag to override the default behavior allows us to
        // handle these exceptions.
        //
        // We throw an error if a property was set explicitly, but we can't do
        // anything with it because the integration is probably not working as the
        // user intended it to.
        if (null === $value) {
            return '';
        }

        if (($value instanceof ApiResource) && (!$value->saveWithParent)) {
            if (!$unsaved) {
                return null;
            }

            if (property_exists($value, 'id')) {
                return $value;
            }

            throw new InvalidArgumentException(
                sprintf('Cannot save property `%s` containing an API resource of type ', $key) .
                    $value::class . ". It doesn't appear to be persisted and is " .
                    'not marked as `saveWithParent`.'
            );
        }

        if (is_array($value)) {
            if (Util::isList($value)) {
                // Sequential array, i.e. a list
                $update = [];
                foreach ($value as $v) {
                    $update[] = $this->serializeParamsValue($v, null, true, $force);
                }

                // This prevents an array that's unchanged from being resent.
                if ($update !== $this->serializeParamsValue($original, null, true, $force, $key)) {
                    return $update;
                }
            } else {
                // Associative array, i.e. a map
                return Util::convertToWildduckObject($value, $this->_opts)->serializeParameters();
            }
        } elseif ($value instanceof self) {
            $update = $value->serializeParameters($force);
            if ($original && $unsaved && $key && static::getAdditiveParams()->includes($key)) {
                return array_merge(self::emptyValues($original), $update);
            }

            return $update;
        } else {
            return $value;
        }
        return null;
    }
    #[Override]
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Returns an associative array with the key and values composing the
     * Wildduck object.
     *
     * @return array the associative array
     */
    public function toArray(): array
    {

        $maybeToArray = static function ($value) {
            if (null === $value) {
                return null;
            }
            return is_object($value) && method_exists($value, 'toArray') ? $value->toArray() : $value;
        };

        return array_reduce(array_keys($this->_values), function (array $acc, $key) use ($maybeToArray) {

            if (str_starts_with((string) $key, '_')) {
                return $acc;
            }

            $value = $this->_values[$key];
            $acc[$key] = is_array($value) && Util::isList($value) ? array_map($maybeToArray, $value) : $maybeToArray($value);

            return $acc;
        }, []);
    }

    /**
     * Returns a pretty JSON representation of the Wildduck object.
     *
     * @return string the JSON representation of the Wildduck object
     */
    public function toJSON(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }
    public function __toString(): string
    {
        $class = static::class;
        return $class . ' JSON: ' . $this->toJSON();
    }

    /**
     * Sets all keys within the WildduckObject as unsaved so that they will be
     * included with an update when `serializeParameters` is called. This
     * method is also recursive, so any WildduckObjects contained as values or
     * which are values in a tenant array are also marked as dirty.
     */
    public function dirty(): void
    {
        $this->_unsavedValues = new Set(array_keys($this->_values));
        foreach ($this->_values as $v) {
            $this->dirtyValue($v);
        }
    }

	/**
	 * @param array|object|string|null $value
	 * @return void
	 */
    private function dirtyValue(array|object|string|null $value): void
    {
        if (is_array($value)) {
            foreach ($value as $v) {
                $this->dirtyValue($v);
            }
        } elseif ($value instanceof self) {
            $value->dirty();
        }
    }

	/**
	 * Produces a deep copy of the given object including support for arrays
	 * and WildduckObjects.
	 *
	 * @param mixed $obj
	 * @return mixed
	 */
    private static function deepCopy(mixed $obj): mixed
    {
        if (is_array($obj)) {
            $copy = [];
            foreach ($obj as $k => $v) {
                $copy[$k] = self::deepCopy($v);
            }

            return $copy;
        }

        if ($obj instanceof self) {
            return $obj::constructFrom(
                self::deepCopy($obj->_values),
                clone $obj->_opts
            );
        }

        return $obj;
    }

    /**
     * Returns a hash of empty values for all the values that are in the given
     * WildduckObject.
     * @param mixed $obj
     * @return array
     */
    protected static function emptyValues(mixed $obj): array
    {
        if (is_array($obj)) {
            $values = $obj;
        } elseif ($obj instanceof self) {
            $values = $obj->_values;
        } else {
            throw new InvalidArgumentException(
                'empty_values got unexpected object type: ' . $obj::class
            );
        }

        return array_fill_keys(array_keys($values), '');
    }

    /**
     * @return null|ApiResponse The last response from the Wildduck API
     */
    public function getLastResponse(): ?ApiResponse
    {
        return $this->_lastResponse;
    }

    /**
     * Sets the last response from the Wildduck API.
     *
     * @param ApiResponse|null $resp
     */
    public function setLastResponse(?ApiResponse $resp): void
    {
        $this->_lastResponse = $resp;
    }

    /**
     * Indicates whether the resource has been deleted on the server.
     * Note that some, but not all, resources can indicate whether they have
     * been deleted.
     *
     * @return bool whether the resource is deleted
     */
    public function isDeleted(): bool
    {
        return $this->_values['deleted'] ?? false;
    }
}
