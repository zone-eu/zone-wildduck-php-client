<?php

namespace Zone\Wildduck\Util;

use AllowDynamicProperties;

#[AllowDynamicProperties]
class Str
{
    /**
     * Check if string ends with a specific string or from an array of strings
     */
    public static function endsWith(string|array $needle, string $haystack): bool
    {
        if (!is_array($needle)) {
            $needle = [$needle];
        }

        foreach ($needle as $n) {
            $pos = strlen($haystack) - strlen((string) $n);
            if (str_contains(substr($haystack, $pos), (string) $n)) {
                return true;
            }
        }

        return false;
    }
}
