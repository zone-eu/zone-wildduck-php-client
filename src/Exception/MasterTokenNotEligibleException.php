<?php

declare(strict_types=1);

namespace Zone\Wildduck\Exception;

/**
 * Thrown when the master API token lacks the authentication assurance needed to create scoped tokens.
 *
 * WildDuck machine-readable error code: MasterTokenNotEligible
 */
class MasterTokenNotEligibleException extends WildduckException
{
    public const string ERROR_CODE = 'MasterTokenNotEligible';

    /**
     * @param string $message
     */
    public function __construct(string $message = '')
    {
        if ($message === '') {
            $message = 'The master API token is no longer eligible to create scoped tokens';
        }

        parent::__construct($message, 403);
    }

    public function getErrorCode(): string
    {
        return self::ERROR_CODE;
    }
}
