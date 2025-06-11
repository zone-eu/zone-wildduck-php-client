<?php

namespace Zone\Wildduck\ApiOperations;

use Zone\Wildduck\WildduckObject;
use Zone\Wildduck\Util\Util;

/**
 * Trait for creatable resources. Adds a `create()` static method to the class.
 *
 * This trait should only be applied to classes that derive from WildduckObject.
 *
 * @deprecated
 */
trait Create
{
	/**
     * @param array|null $params
	 * @param array|string|null $options
	 * @return WildduckObject
	 *
	 * @deprecated
	 */
	public static function create(?array $params = null, array|null|string $options = null): WildduckObject
    {
        self::_validateParams($params);
        $url = static::classUrl();

        [$response, $opts] = static::_staticRequest('post', $url, $params, $options);
        $obj = Util::convertToWildduckObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }
}
