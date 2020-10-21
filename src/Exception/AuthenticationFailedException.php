<?php

namespace Zone\Wildduck\Exception;

class AuthenticationFailedException extends WildduckException
{
    public function __construct(string $message = '', int $code = 100) // 100='error.authentication'
    {
        parent::__construct($message, $code);
    }
}