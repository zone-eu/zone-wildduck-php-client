<?php

namespace Wildduck\Exceptions;

class RequestFailedException extends \Exception {
    private $errorCode = null;

    public function __construct($message = 'Request failed', $code = null, \Exception $previous = null)
    {
        $this->errorCode = $code;
        parent::__construct($message, 0, $previous);
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }
};
