<?php

namespace Zone\Wildduck\Util;

use AllowDynamicProperties;

/**
 * A basic random generator. This is in a separate class so we the generator
 * can be injected as a dependency and replaced with a mock in tests.
 */
#[AllowDynamicProperties]
class RandomGenerator
{
    /**
     * Returns a random value between 0 and $max.
     *
     * @param float $max (optional)
     *
     * @return int|float
     */
    public function randFloat(float $max = 1.0): int|float
    {
        return mt_rand() / mt_getrandmax() * $max;
    }

    /**
     * Returns a v4 UUID.
     */
    public function uuid(): string
    {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
