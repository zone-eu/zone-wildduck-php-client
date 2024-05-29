<?php

namespace Zone\Wildduck\Util;

use AllowDynamicProperties;
use Illuminate\Support\Facades\Log;
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
     * @param array $array
     *
     * @return bool true if the given object is a list
     */
    public static function isList(array $array): bool
    {
        if ($array === []) {
            return true;
        }
        return array_keys($array) === range(0, count($array) - 1);
    }

    /**
     * Converts a response from the Wildduck API to the corresponding PHP object.
     *
     * @param array|string|bool|object $resp the response from the Wildduck API
     * @param array|RequestOptions|null $opts
     *
     * @return mixed
     */
    public static function convertToWildduckObject(array|string|bool|object $resp, array|RequestOptions|null $opts = null): mixed
    {
        $types = ObjectTypes::MAPPING;
        if (is_array($resp) && self::isCollection($resp)) {
            return new Collection2($resp, $opts);
        }

	    if (is_array($resp) && self::isList($resp)) {
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
     * @param array|null $values
     * @return array|null
     */
    public static function arrayToIds(array|null $values): null|array
    {
        /*if ($values instanceof ApiResource) {
            return $values->id;
        }*/
	    $results = [];
        if (is_array($values) && self::isList($values)) {
            foreach ($values as $value) {
                $results[] = self::test($value);
            }
	        return $results;
        }

		if (is_array($values)) {
            foreach ($values as $k => $value) {
                if (null === $value) {
                    continue;
                }
                $results[$k] = self::test($value);
            }
			return $results;
        }

        return null;
    }

	private static function test ($values) {
		if(is_array($values)){
			return self::arrayToIds($values);
		}

		return $values;
	}

    public static function encodeParameters(array $params): string
    {
        $flattenedParams = self::flattenParams($params);
        $pieces = [];
        foreach ($flattenedParams as $param) {
            [$key, $value] = $param;
	        Log::debug('$param' , [print_r(['key' => $key , 'value' => $value], true)]);

            $pieces[] = self::urlEncode($key) . '=' . self::urlEncode($value);
        }

        return implode('&', $pieces);
    }

    public static function flattenParams(array $params, null|string $parentKey = null): array
    {
        $result = [];
        foreach ($params as $key => $value) {
            $calculatedKey = $parentKey ? sprintf('%s[%s]', $parentKey, $key) : $key;

            if (is_array($value) && self::isList($value)) {
                $result[] = array_merge(...self::flattenParamsList($value, $calculatedKey));
            } elseif (is_array($value)) {
               $result[] = array_merge(...self::flattenParams($value, $calculatedKey));
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
            if (is_array($elem) && self::isList($elem)) {
                $result[] = array_merge(...self::flattenParamsList($elem, $calculatedKey));
            } elseif (is_array($elem)) {
                $result[] = array_merge(...self::flattenParams($elem, sprintf('%s[%s]', $calculatedKey, $i)));
            } else {
                $result[] = [sprintf('%s[%s]', $calculatedKey, $i), $elem];
            }
        }

        return $result;
    }

    /**
     * @param string|array $key a string to URL-encode
     *
     * @return string the URL-encoded string
     */
    public static function urlEncode(string|array $key): string
    {
        if (is_array($key)) {
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
