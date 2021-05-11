<?php

namespace Zone\Wildduck\Exception;

class RequestFailedException extends WildduckException
{

    private ?string $errorCode;

    public function __construct(string $message, string $code = 'internal')
    {
        $this->errorCode = $code;
        parent::__construct($message, 0);
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }
}
