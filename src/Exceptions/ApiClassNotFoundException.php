<?php

namespace Wildduck\Exceptions;

use Throwable;

class ApiClassNotFoundException extends \Exception implements \Throwable
{

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
