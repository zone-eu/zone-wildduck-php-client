<?php

namespace Zone\Wildduck\ApiOperations;

use Zone\Wildduck\Util\RequestOptions;

/**
 * Trait for retrievable resources. Adds a `retrieve()` static method to the
 * class.
 *
 * This trait should only be applied to classes that derive from WildduckObject.
 *
 * @deprecated
 */
trait Retrieve
{
    /**
     * @param array|string $id the ID of the API resource to retrieve,
     *     or an options array containing an `id` key
     *
     */
    public static function retrieve(array|string $id, array|null|RequestOptions|string $opts = null): static
    {
        $opts = RequestOptions::parse($opts);
        $instance = new static($id, $opts);
        $instance->refresh();

        return $instance;
    }
}
