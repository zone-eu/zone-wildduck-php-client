<?php

namespace Zone\Wildduck\Service\Traits;

use Zone\Wildduck\Exception\MissingGlobalAccessTokenException;
use Zone\Wildduck\WildduckClientInterface;

/**
 * Trait to handle checking of global access token
 *
 * This trait should only be applied to a class that has requests that don't require user specific tokens.
 */
trait RequiresGlobalToken
{
    /**
     *
     * @throws MissingGlobalAccessTokenException if $client has a falsy token
     */
    public function __construct(WildduckClientInterface $client)
    {
        if (!$client->getAccessToken()) {
            throw new MissingGlobalAccessTokenException(
                'Global access token must be set for this resource: ' . static::class
            );
        }

        parent::__construct($client);
    }
}
