<?php

namespace Zone\Wildduck\ApiOperations;

use Zone\Wildduck\Collection2;
use Zone\Wildduck\Exception\UnexpectedValueException;
use Zone\Wildduck\Util\Util;

/**
 * Trait for listable resources. Adds a `all()` static method to the class.
 *
 * This trait should only be applied to classes that derive from WildduckObject.
 *
 * @deprecated
 */
trait All
{
    /**
     * @param array|null $params Must be KV pairs when not uploading files otherwise anything is allowed, string is expected for file upload. Can be nested for arrays and hashes
     * @param null|array|string $opts
     *
     * @return Collection2 of ApiResources
     *
     * @throws UnexpectedValueException
     *
     * @Deprecated No longer used by internal code.
     */
    private static function all(?array $params = null, array|null|string $opts = null): Collection2
    {
        self::_validateParams($params);
        $url = static::classUrl();

        [$response, $opts] = static::_staticRequest('get', $url, $params, $opts);
        $obj = Util::convertToWildduckObject($response->json, $opts);
        if (!($obj instanceof Collection2)) {
            throw new UnexpectedValueException(
                'Expected type ' . Collection2::class . ', got "' . $obj::class . '" instead.'
            );
        }

        $obj->setLastResponse($response);
        $obj->setFilters($params);

        return $obj;
    }
}
