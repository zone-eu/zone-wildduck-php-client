<?php

namespace Zone\Wildduck\Exception;

class InvalidAccessTokenException extends WildduckException
{

    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}