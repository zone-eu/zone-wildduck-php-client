<?php

namespace Zone\Wildduck\ApiOperations;

use Zone\Wildduck\Collection2;
use Zone\Wildduck\WildduckObject;
use Zone\Wildduck\Util\Util;

/**
 * Trait for creatable resources. Adds a `create()` static method to the class.
 *
 * This trait should only be applied to classes that derive from WildduckObject.
 */
trait Create
{
    /**
     * @param null|array $params
     * @param null|array|string $options
     *
     * @return array|static|Collection2|WildduckObject
     */
    public static function create($params = null, $options = null)
    {
        self::_validateParams($params);
        $url = static::classUrl();

        list($response, $opts) = static::_staticRequest('post', $url, $params, $options);
        $obj = Util::convertToWildduckObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }
}
