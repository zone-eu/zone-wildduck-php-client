<?php

namespace Zone\Wildduck\Exception;

use AllowDynamicProperties;

#[AllowDynamicProperties]
class RequestFailedException extends WildduckException
{
    public function __construct(string $message, private readonly ?string $errorCode = 'internal', int $rCode = 0, ?int $httpStatus = null)
    {
        parent::__construct($message, $rCode, null, $httpStatus ?? ($rCode ?: null));
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }
}
