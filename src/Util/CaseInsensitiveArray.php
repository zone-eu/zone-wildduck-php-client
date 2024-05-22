<?php

namespace Zone\Wildduck\Util;

use AllowDynamicProperties;
use Override;
use ArrayAccess;
use Countable;
use IteratorAggregate;
use ArrayIterator;

/**
 * CaseInsensitiveArray is an array-like class that ignores case for keys.
 *
 * It is used to store HTTP headers. Per RFC 2616, section 4.2:
 * Each header field consists of a name followed by a colon (":") and the field value. Field names
 * are case-insensitive.
 *
 * In the context of stripe-php, this is useful because the API will return headers with different
 * case depending on whether HTTP/2 is used or not (with HTTP/2, headers are always in lowercase).
 */
#[AllowDynamicProperties]
class CaseInsensitiveArray implements ArrayAccess, Countable, IteratorAggregate
{
    private array $container;

    public function __construct(array $initial_array = [])
    {
        $this->container = array_change_key_case($initial_array, CASE_LOWER);
    }

    #[Override]
    public function count(): int
    {
        return count($this->container);
    }

    #[Override]
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->container);
    }

    #[Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $offset = $this->maybeLowercase($offset);
        if (null === $offset) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    #[Override]
    public function offsetExists(mixed $offset): bool
    {
        $offset = $this->maybeLowercase($offset);

        return isset($this->container[$offset]);
    }

    #[Override]
    public function offsetUnset(mixed $offset): void
    {
        $offset = $this->maybeLowercase($offset);
        unset($this->container[$offset]);
    }

    #[Override]
    public function offsetGet(mixed $offset): array|null
    {
        $offset = $this->maybeLowercase($offset);

        return $this->container[$offset] ?? null;
    }

	/**
	 * @param string|null $v
	 * @return string|null
	 */
	private function maybeLowercase(string|null $v): string|null
    {
        if (is_string($v)) {
            return strtolower($v);
        }

        return $v;
    }
}
