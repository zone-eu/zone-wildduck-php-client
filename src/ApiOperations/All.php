<?php

namespace Zone\Wildduck\ApiOperations;

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
     * @throws \Zone\Wildduck\Exception\ApiErrorException if the request fails
     *
     * @return \Zone\Wildduck\Collection of ApiResources
     */
    public static function all($params = null, $opts = null)
    {
        self::_validateParams($params);
        $url = static::classUrl();

        list($response, $opts) = static::_staticRequest('get', $url, $params, $opts);
        $obj = \Zone\Wildduck\Util\Util::convertToWildduckObject($response->json, $opts);
        if (!($obj instanceof \Zone\Wildduck\Collection)) {
            throw new \Zone\Wildduck\Exception\UnexpectedValueException(
                'Expected type ' . \Zone\Wildduck\Collection::class . ', got "' . \get_class($obj) . '" instead.'
            );
        }
        $obj->setLastResponse($response);
        $obj->setFilters($params);

        return $obj;
    }
}
