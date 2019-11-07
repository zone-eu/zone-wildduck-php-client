<?php

namespace Wildduck\Exceptions;

use Psr\Http\Message\ResponseInterface;

class HttpErrorResponseException extends \Exception
{
    private $response;

    public function __construct($message, $code, \Exception $previous = null, ResponseInterface $response = null)
    {
        parent::__construct($message, $code, $previous);

        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}