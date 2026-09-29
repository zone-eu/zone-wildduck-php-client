<?php

declare(strict_types=1);

namespace Zone\Wildduck\Exception;

/**
 * Thrown when multi-factor authentication must be completed before a scoped token can be created.
 *
 * WildDuck machine-readable error code: MfaRequired
 */
class MfaRequiredException extends WildduckException
{
    public const string ERROR_CODE = 'MfaRequired';

    /**
     * @param string $message
     */
    public function __construct(string $message = '')
    {
        if ($message === '') {
            $message = 'Multi-factor authentication must be completed first';
        }

        parent::__construct($message, 403);
    }

    public function getErrorCode(): string
    {
        return self::ERROR_CODE;
    }
}
