<?php

namespace Zone\Wildduck\ApiOperations;

use Zone\Wildduck\WildduckObject;
use Zone\Wildduck\Util\Util;

/**
 * Trait for resources that have nested resources.
 *
 * This trait should only be applied to classes that derive from WildduckObject.
 */
trait NestedResource
{
    /**
     * @param string $method
     * @param string $url
     * @param array|null $params
     * @param array|null|string $options
     *
     * @return WildduckObject
     */
    protected static function _nestedResourceOperation(string $method, string $url, array|null $params = null, array|string|null $options = null): WildduckObject
    {
        self::_validateParams($params);

        [$response, $opts] = static::_staticRequest($method, $url, $params, $options);
        $obj = Util::convertToWildduckObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }

    /**
     * @param string $id
     * @param string $nestedPath
     * @param null|string $nestedId
     *
     * @return string
     */
    protected static function _nestedResourceUrl(string $id, string $nestedPath, null|string $nestedId = null): string
    {
        $url = static::resourceUrl($id) . $nestedPath;
        if (null !== $nestedId) {
            $url .= '/' . $nestedId;
        }

        return $url;
    }

    /**
     * @param string $id
     * @param string $nestedPath
     * @param array|null $params
     * @param array|null|string $options
     *
     * @return WildduckObject
     */
    protected static function _createNestedResource(string $id, string $nestedPath, array|null $params = null, array|null|string $options = null): WildduckObject
    {
        $url = static::_nestedResourceUrl($id, $nestedPath);

        return self::_nestedResourceOperation('post', $url, $params, $options);
    }

    /**
     * @param string $id
     * @param string $nestedPath
     * @param null|string $nestedId
     * @param array|null $params
     * @param array|null|string $options
     *
     * @return WildduckObject
     */
    protected static function _retrieveNestedResource(string $id, string $nestedPath, null|string $nestedId, array|null $params = null, array|null|string $options = null): WildduckObject
    {
        $url = static::_nestedResourceUrl($id, $nestedPath, $nestedId);

        return self::_nestedResourceOperation('get', $url, $params, $options);
    }

    /**
     * @param string $id
     * @param string $nestedPath
     * @param null|string $nestedId
     * @param array|null $params
     * @param array|string|null $options
     *
     * @return WildduckObject
     */
    protected static function _updateNestedResource(string $id, string $nestedPath, null|string $nestedId, array|null $params = null, array|null|string $options = null): WildduckObject
    {
        $url = static::_nestedResourceUrl($id, $nestedPath, $nestedId);

        return self::_nestedResourceOperation('post', $url, $params, $options);
    }

    /**
     * @param string $id
     * @param string $nestedPath
     * @param null|string $nestedId
     * @param array|null $params
     * @param array|null|string $options
     *
     * @return WildduckObject
     */
    protected static function _deleteNestedResource(string $id, string $nestedPath, null|string $nestedId, array|null $params = null, array|null|string $options = null): WildduckObject
    {
        $url = static::_nestedResourceUrl($id, $nestedPath, $nestedId);

        return self::_nestedResourceOperation('delete', $url, $params, $options);
    }

    /**
     * @param string $id
     * @param string $nestedPath
     * @param array|null $params
     * @param array|null|string $options
     *
     * @return WildduckObject
     */
    protected static function _allNestedResources(string $id, string $nestedPath, array|null $params = null, array|null|string $options = null): WildduckObject
    {
        $url = static::_nestedResourceUrl($id, $nestedPath);

        return self::_nestedResourceOperation('get', $url, $params, $options);
    }
}
