<?php

declare(strict_types=1);

namespace Zone\Wildduck\Exception;

/**
 * Thrown when a scoped token is requested for a scope the server does not support.
 *
 * WildDuck machine-readable error code: UnsupportedAuthScope
 */
class UnsupportedAuthScopeException extends WildduckException
{
    public const string ERROR_CODE = 'UnsupportedAuthScope';

    /**
     * @param string $message
     */
    public function __construct(string $message = '')
    {
        if ($message === '') {
            $message = 'Unsupported authentication scope';
        }

        parent::__construct($message, 400);
    }

    public function getErrorCode(): string
    {
        return self::ERROR_CODE;
    }
}
