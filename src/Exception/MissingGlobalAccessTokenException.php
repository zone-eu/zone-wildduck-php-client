<?php

namespace Zone\Wildduck\Exception;

class MissingGlobalAccessTokenException extends WildduckException
{

    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}