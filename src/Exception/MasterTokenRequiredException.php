<?php

declare(strict_types=1);

namespace Zone\Wildduck\Exception;

/**
 * Thrown when an operation requires the user's master API token but the request was not authenticated with one.
 *
 * WildDuck machine-readable error code: MasterTokenRequired
 */
class MasterTokenRequiredException extends WildduckException
{
    public const string ERROR_CODE = 'MasterTokenRequired';

    /**
     * @param string $message
     */
    public function __construct(string $message = '')
    {
        if ($message === '') {
            $message = 'A master API token is required';
        }

        parent::__construct($message, 403);
    }

    public function getErrorCode(): string
    {
        return self::ERROR_CODE;
    }
}
