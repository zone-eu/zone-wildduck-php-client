<?php

namespace Wildduck\Exceptions;

class AuthenticationFailedException extends \Exception
{

    public function __construct(string $message = 'Authentication Failed', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
