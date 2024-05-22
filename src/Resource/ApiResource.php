<?php

namespace Zone\Wildduck\Resource;

use Override;
use Zone\Wildduck\ApiOperations\Request;
use Zone\Wildduck\ApiRequestor;
use Zone\Wildduck\Util\Set;
use Zone\Wildduck\Util\Util;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\UnexpectedValueException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\Util\Str;
use Zone\Wildduck\Wildduck;
use Zone\Wildduck\WildduckObject;

/**
 * Class ApiResource.
 */
abstract class ApiResource extends WildduckObject
{
    use Request;

    public const string OBJECT_NAME = '';

    //public string|array|int|null $id;

    /**
     * @return Set A list of fields that can be their own type of
     * API resource (say a nested card under an account for example), and if
     * that resource is set, it should be transmitted to the API on a create or
     * update. Doing so is not the default behavior because API resources
     * should normally be persisted on their own REST endpoints.
     */
	protected static function getSavedNestedResources(): Set
    {
        static $savedNestedResources = null;
        if (null === $savedNestedResources) {
            $savedNestedResources = new Set();
        }

        return $savedNestedResources;
    }

    /**
     * @var bool A flag that can be set a behavior that will cause this
     * resource to be encoded and sent up along with an update of its parent
     * resource. This is usually not desirable because resources are updated
     * individually on their own endpoints, but there are certain cases,
     * replacing a customer's source for example, where this is allowed.
     */
    public bool $saveWithParent = false;

    /**
     * @param mixed $name
     * @param mixed $v
     *
     * @return void
     */
    #[Override]
    public function __set(mixed $name, mixed $v): void
    {
        parent::__set($name, $v);
        $v = $this->{$name};
        if (!static::getSavedNestedResources()->includes($name)) {
            return;
        }
        if (!$v instanceof self) {
            return;
        }
        $v->saveWithParent = true;
    }

    /**
     * @return ApiResource the refreshed resource
     * @throws UnexpectedValueException
     * @throws AuthenticationFailedException
     * @throws RequestFailedException
     * @throws ValidationException
     * @throws InvalidAccessTokenException
     * @throws InvalidDatabaseException
     * @throws ApiConnectionException
     */
	protected function refresh(): ApiResource
    {
        $requestor = new ApiRequestor($this->_opts->apiKey, static::baseUrl());
        $url = $this->instanceUrl();

        [$response, $this->_opts->apiKey] = $requestor->request(
            'get',
            $url,
            $this->_retrieveOptions,
            $this->_opts->headers
        );
        $this->setLastResponse($response);
        $this->refreshFrom($response->json, $this->_opts);

        return $this;
    }

    /**
     * @return string the base URL for the given class
     */
	protected static function baseUrl(): string
    {
        return Wildduck::$apiBase;
    }

    /**
     * @return string the endpoint URL for the given class
     */
	protected static function classUrl(): string
    {
        // Replace dots with slashes for namespaced resources, e.g. if the object's name is
        // "foo.bar", then its URL will be "/v1/foo/bars".
        $base = str_replace('.', '/', static::OBJECT_NAME);

        // Special case for "es" endings
        $pluralize = ['mailbox', 'address'];
        if (Str::endsWith($pluralize, $base)) {
            return sprintf('/%ses', $base);
        }

        return sprintf('/%ss', $base);
    }

    /**
     * @param null|string $id the ID of the resource
     *
     * @throws UnexpectedValueException if $id is null
     *
     * @return string the instance endpoint URL for the given class
     */
	protected static function resourceUrl(null|string $id): string
    {
        if (null === $id) {
            $class = static::class;
            $message = 'Could not determine which URL to request: '
               . sprintf('%s instance has invalid ID: %s', $class, $id);

            throw new UnexpectedValueException($message);
        }

        $id = Util::utf8($id);
        $base = static::classUrl();
        $extn = urlencode((string) $id);

        return sprintf('%s/%s', $base, $extn);
    }

    /**
     * @return string the full API URL for this API resource
     */
    protected function instanceUrl(): string
    {
        return static::resourceUrl($this['id']);
    }
}
