<?php

namespace Zone\Wildduck\ApiOperations;

use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\WildduckObject;
use Zone\Wildduck\Util\Util;

use function is_countable;

/**
 * Trait for updatable resources. Adds an `update()` static method and a
 * `save()` method to the class.
 *
 * This trait should only be applied to classes that derive from WildduckObject.
 *
 * @deprecated
 */
trait Update
{
    /**
     * @param string $id the ID of the resource to update
     * @param array|null $params
     * @param array|null|string $opts
     *
     * @return WildduckObject|array the updated resource
     *
     * @deprecated
     */
    public static function update(string $id, array|null $params = null, array|null|string $opts = null): WildduckObject|array
    {
        self::_validateParams($params);
        $url = static::resourceUrl($id);

        [$response, $opts] = static::_staticRequest('put', $url, $params, $opts);
        $obj = Util::convertToWildduckObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }

    /**
     * @param array|null|string $opts
     *
     * @return static|bool the saved resource
     *
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws InvalidDatabaseException
     * @throws RequestFailedException
     * @throws ValidationException
     *
     * @deprecated
     */
    public function save(array|null|string $opts = null): bool|static
    {
        $params = $this->serializeParameters();
        if (is_countable($params) && count($params) > 0) {
            $url = $this->instanceUrl();
            [$response] = $this->_request('put', $url, $params, $opts);
            if (!isset($response['success'])) {
                return false;
            }

            $this->refresh();
        }

        return $this;
    }
}
