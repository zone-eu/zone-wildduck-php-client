<?php

namespace Wildduck\Exceptions;

class RequestFailedException extends \Exception {
    public function __construct($message = 'Request failed', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
};
