<?php

namespace Zone\Wildduck\Exception;

class RequestFailedException extends WildduckException
{

    public function __construct(string $message, string $code = 'error.request')
    {
        parent::__construct($message, $code);
    }
}