<?php

namespace Zone\Wildduck\Exception;

use AllowDynamicProperties;

#[AllowDynamicProperties]
class UnexpectedValueException extends \UnexpectedValueException
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
}
