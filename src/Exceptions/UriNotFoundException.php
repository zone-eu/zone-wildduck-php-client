<?php

namespace Wildduck\Exceptions;

class UriNotFoundException extends \Exception implements \Throwable
{

    public function __construct($keyword, $code = 0, \Exception $previous = null)
    {
        parent::__construct("Uri with keyword $keyword not found", $code, $previous);
    }
}
