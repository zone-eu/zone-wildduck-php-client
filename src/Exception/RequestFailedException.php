<?php

namespace Zone\Wildduck\Exception;

class RequestFailedException extends WildduckException
{

    private ?string $errorCode;

    public function __construct(string $message, string $code = 'internal', int $rCode = 0)
    {
        $this->errorCode = $code;
        parent::__construct($message, $rCode);
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }
}
