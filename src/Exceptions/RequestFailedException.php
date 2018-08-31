<?php

namespace Wildduck\Exceptions;

class RequestFailedException extends \Exception
{

    private $data;

    public function __construct($message = 'HTTP Error', $code = 0, \Exception $previous = null, array $data)
    {
        $this->data = $data;
        return parent::__construct($message, $code, $previous);
    }

    public function getData()
    {
        return $this->data;
    }
}