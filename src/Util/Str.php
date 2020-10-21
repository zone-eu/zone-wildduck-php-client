<?php

namespace Zone\Wildduck\Util;

class Str
{

    /**
     * Check if string ends with a specific string or from an array of strings
     * @param string|array $needle
     * @param string $haystack
     * @return bool
     */
    public static function endsWith($needle, $haystack)
    {
        if (!is_array($needle)) {
            $needle = [$needle];
        }

        foreach ($needle as $n) {
            $pos = strlen($haystack) - strlen($n);
            if (strpos($haystack, $n, $pos) !== false) {
                return true;
            }
        }

        return false;
    }
}