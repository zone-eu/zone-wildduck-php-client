<?php

namespace Zone\Wildduck\Util;

use AllowDynamicProperties;

use Zone\Wildduck\ApiResource;
use Zone\Wildduck\Collection2;
use Zone\Wildduck\WildduckObject;

#[AllowDynamicProperties]
abstract class Util
{
    private static ?bool $isMbstringAvailable = null;

    private static ?bool $isHashEqualsAvailable = null;

    public static function isCollection(array $resp): bool
    {
        return isset($resp['results']);
    }

    /**
     * Whether the provided array (or other) is a list rather than a dictionary.
     * A list is defined as an array for which all the keys are consecutive
     * integers starting at 0. Empty arrays are considered to be lists.
     *
     * @param array|mixed $array
     *
     * @return bool true if the given object is a list
     */
    public static function isList(mixed $array): bool
    {
        if (!is_array($array)) {
            return false;
        }

        if ($array === []) {
            return true;
        }
        return array_keys($array) === range(0, count($array) - 1);
    }

    /**
     * Converts a response from the Wildduck API to the corresponding PHP object.
     *
     * @param array|string|bool $resp the response from the Wildduck API
     * @param array|RequestOptions|null $opts
     *
     * @return mixed
     */
    public static function convertToWildduckObject(array|string|bool $resp, array|RequestOptions $opts = null): mixed
    {
        $types = ObjectTypes::MAPPING;
        if (is_array($resp) && self::isCollection($resp)) {
            return new Collection2($resp, $opts);
        }

        if (self::isList($resp)) {
            $mapped = [];
            foreach ($resp as $i) {
                $mapped[] = self::convertToWildduckObject($i, $opts);
            }

            return $mapped;
        }
        if ($opts && $opts->object) {
            $class = $types[$opts->object];
        } elseif (isset($resp['object'], $types[$resp['object']]) && is_string($resp['object'])) {
            $class = $types[$resp['object']];
        } else {
            $class = WildduckObject::class;
        }

        return is_array($resp) ? $class::constructFrom($resp, $opts) : $resp;
    }

    /**
     * @param mixed|string $value a string to UTF8-encode
     *
     * @return mixed|string the UTF8-encoded string, or the object passed in if
     *    it wasn't a string
     */
    public static function utf8(mixed $value): mixed
    {
        if (null === self::$isMbstringAvailable) {
            self::$isMbstringAvailable = function_exists('mb_detect_encoding');

            if (!self::$isMbstringAvailable) {
                trigger_error('It looks like the mbstring extension is not enabled. ' .
                    'UTF-8 strings will not properly be encoded. Ask your system ' .
                    'administrator to enable the mbstring extension, or write to ' .
                    'support@stripe.com if you have any questions.', E_USER_WARNING);
            }
        }

        if (is_string($value) && self::$isMbstringAvailable && 'UTF-8' !== mb_detect_encoding($value, 'UTF-8', true)) {
            return mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
        }

        return $value;
    }

    /**
     * Compares two strings for equality. The time taken is independent of the
     * number of characters that match.
     *
     * @param string $a one of the strings to compare
     * @param string $b the other string to compare
     *
     * @return bool true if the strings are equal, false otherwise
     */
    public static function secureCompare(string $a, string $b): bool
    {
        if (null === self::$isHashEqualsAvailable) {
            self::$isHashEqualsAvailable = function_exists('hash_equals');
        }

        if (self::$isHashEqualsAvailable) {
            return hash_equals($a, $b);
        }

        if (strlen($a) !== strlen($b)) {
            return false;
        }

        $result = 0;
        for ($i = 0, $iMax = strlen($a); $i < $iMax; ++$i) {
            $result |= ord($a[$i]) ^ ord($b[$i]);
        }

        return 0 === $result;
    }

    /**
     * Recursively goes through an array of parameters. If a parameter is an instance of
     * ApiResource, then it is replaced by the resource's ID.
     * Also clears out null values.
     *
     * @param object|array|string|int $h
     * @return array|int|string|object|null
     */
    public static function objectsToIds(object|array|string|int $h): array|int|null|string|object
    {
        if ($h instanceof ApiResource) {
            return $h->id;
        }

        if (static::isList($h)) {
            $results = [];
            foreach ($h as $v) {
                $results[] = static::objectsToIds($v);
            }

            return $results;
        }

        if (is_array($h)) {
            $results = [];
            foreach ($h as $k => $v) {
                if (null === $v) {
                    continue;
                }

                $results[$k] = static::objectsToIds($v);
            }

            return $results;
        }

        return $h;
    }

    public static function encodeParameters(array $params): string
    {
        $flattenedParams = self::flattenParams($params);
        $pieces = [];
        foreach ($flattenedParams as $param) {
            [$k, $v] = $param;
            $pieces[] = self::urlEncode($k) . '=' . self::urlEncode($v);
        }

        return implode('&', $pieces);
    }

    public static function flattenParams(array $params, null|string $parentKey = null): array
    {
        $result = [];

        foreach ($params as $key => $value) {
            $calculatedKey = $parentKey ? sprintf('%s[%s]', $parentKey, $key) : $key;

            if (self::isList($value)) {
                $result = [$result, self::flattenParamsList($value, $calculatedKey)];
            } elseif (is_array($value)) {
                $result = [$result, self::flattenParams($value, $calculatedKey)];
            } else {
                $result[] = [$calculatedKey, $value];
            }
        }

        return $result;
    }

    public static function flattenParamsList(array $value, string $calculatedKey): array
    {
        $result = [];

        foreach ($value as $i => $elem) {
            if (self::isList($elem)) {
                $result = array_merge(...self::flattenParamsList($elem, $calculatedKey));
            } elseif (is_array($elem)) {
                $result = array_merge(...self::flattenParams($elem, sprintf('%s[%s]', $calculatedKey, $i)));
            } else {
                $result[] = [sprintf('%s[%s]', $calculatedKey, $i), $elem];
            }
        }

        return $result;
    }

    /**
     * @param string|object $key a string to URL-encode
     *
     * @return string the URL-encoded string
     */
    public static function urlEncode(string|object $key): string
    {
        if (is_object($key)) {
            $key = json_encode($key);
        }

        $s = urlencode((string) $key);

        // Don't use strict form encoding by changing the square bracket control
        // characters back to their literals. This is fine by the server, and
        // makes these parameter strings easier to read.
        return str_replace(['%7B', '%7D', '%5D', '%5B'], ['{', '}', ']', '['], $s);
    }


	/**
	 * @param array $response
	 * @return array{0: string, 1: array}
	 */
	public static function normalizeId(array $response): array
	{
		if (isset($response['id'])) {
			$id = $response['id'];
			unset($response['id']);
		} else {
			$id = '';
		}

		return [$id, $response];
	}

    /**
     * Returns UNIX timestamp in milliseconds.
     *
     * @return int current time in millis
     */
    public static function currentTimeMillis(): int
    {
        return (int) round(microtime(true) * 1000);
    }
}
