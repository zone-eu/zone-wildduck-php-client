<?php

namespace Wildduck\Util;

use Wildduck\Exceptions\UriNotFoundException;

class Uri
{
    private static $uris = [
        'address.create' => '/users/:user/address',
        'authentication.authenticate' => '/authenticate',
        'users.create' => '/users',
        'users.delete' => '/users/:id'
    ];

    public static function get($keyword, $args = [])
    {
        if (!array_key_exists($keyword, self::$uris)) {
            throw new UriNotFoundException($keyword);
        }

        $uri = self::$uris[$keyword];

        if (strpos($uri, ':') !== false) {
            $uri = preg_replace_callback('/(:[a-z]+)/', function ($matches) use ($args) {
                $match = substr($matches[0], 1, strlen($matches[0]));

                // If the matched placeholder was not found from supplied arguments, keep placeholder intact
                if (!array_key_exists($match, $args)) {
                    return $matches[0];
                }

                return $args[$match];
            }, $uri);
        }

        return $uri;
    }
}
