<?php

declare(strict_types=1);

namespace Zone\Wildduck\Exception;

/**
 * Thrown when the account must replace its temporary password before scoped tokens can be created.
 *
 * WildDuck machine-readable error code: PasswordChangeRequired
 */
class PasswordChangeRequiredException extends WildduckException
{
    public const string ERROR_CODE = 'PasswordChangeRequired';

    /**
     * @param string $message
     */
    public function __construct(string $message = '')
    {
        if ($message === '') {
            $message = 'The temporary password must be replaced before creating a scoped token';
        }

        parent::__construct($message, 403);
    }

    public function getErrorCode(): string
    {
        return self::ERROR_CODE;
    }
}
