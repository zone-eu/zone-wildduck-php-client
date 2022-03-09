<?php

namespace Zone\Wildduck\ApiOperations;

use Zone\Wildduck\Collection2;
use Zone\Wildduck\Exception\UnexpectedValueException;
use Zone\Wildduck\Util\Util;

/**
 * Trait for listable resources. Adds a `all()` static method to the class.
 *
 * This trait should only be applied to classes that derive from WildduckObject.
 */
trait All
{
    /**
     * @param null|array $params
     * @param null|array|string $opts
     *
     * @return Collection2 of ApiResources
     *
     * @throws UnexpectedValueException
     */
    public static function all($params = null, $opts = null)
    {
        self::_validateParams($params);
        $url = static::classUrl();

        list($response, $opts) = static::_staticRequest('get', $url, $params, $opts);
        $obj = Util::convertToWildduckObject($response->json, $opts);
        if (!($obj instanceof Collection2)) {
            throw new UnexpectedValueException(
                'Expected type ' . Collection2::class . ', got "' . \get_class($obj) . '" instead.'
            );
        }
        $obj->setLastResponse($response);
        $obj->setFilters($params);

        return $obj;
    }
}
