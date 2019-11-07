<?php

namespace Wildduck\Exceptions;

use Psr\Http\Message\ResponseInterface;

class HttpErrorResponseException extends \Exception
{
    private $response;

    public function __construct($message, $code, \Exception $previous = null, ResponseInterface $response = null)
    {
        parent::__construct($message, $code, $previous);

        if (null === $this->response = json_decode($response->getBody()->getContents(), true)) {
            $this->response = $response->getBody()->getContents();
        }
    }

    public function getResponse()
    {
        return $this->response;
    }
}