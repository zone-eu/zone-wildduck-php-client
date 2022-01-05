<?php

namespace Zone\Wildduck\Exception;

use Throwable;

class InvalidDatabaseException extends WildduckException
{


    public function __construct($message = "", $code = 500)
    {
        parent::__construct($message, $code);
    }
}