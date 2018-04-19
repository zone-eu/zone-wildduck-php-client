<?php

namespace Wildduck;

use Wildduck\Api\Address;
use Wildduck\Api\Authentication;
use Wildduck\Exceptions\ApiClassNotFoundException;

/**
 * Main wrapper for Wildduck API Client.
 *
 * @package Wildduck
 *
 * @method Address address()
 * @method Authentication authentication()
 */
class Client
{
    /**
     * @param string $name
     * @return mixed
     * @throws ApiClassNotFoundException
     */
    public static function __callStatic(string $name)
    {
        $class = "Wildduck\\Api\\" . ucfirst($name);
        if (!class_exists($class)) {
            throw new ApiClassNotFoundException("API class $class not found");
        }

        return new $class;
    }
}
