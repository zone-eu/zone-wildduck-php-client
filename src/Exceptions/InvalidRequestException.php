<?php

namespace Wildduck\Exceptions;

class InvalidRequestException extends \Exception
{

    private $errors;

    public function __construct(array $errors, $code = 0, \Throwable $previous = null)
    {
        $class = get_called_class();
        $this->errors = $errors;

        parent::__construct("Validation errors in $class", $code, $previous);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
