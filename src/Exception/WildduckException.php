<?php

namespace Zone\Wildduck\Exception;

use AllowDynamicProperties;
use Exception;

#[AllowDynamicProperties]
class WildduckException extends Exception
{
    /**
     * @param int $code Exception code
     * @param int|null $httpStatus HTTP status of the WildDuck response that caused this error, null when the error did not come from a WildDuck HTTP response
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null, private readonly ?int $httpStatus = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * HTTP status of the WildDuck response that caused this error, null when the
     * error did not come from a WildDuck HTTP response
     */
    public function getHttpStatus(): ?int
    {
        return $this->httpStatus;
    }

    /**
     * Machine-readable WildDuck error code when the subclass maps one, otherwise null
     */
    public function getErrorCode(): ?string
    {
        return null;
    }
}
