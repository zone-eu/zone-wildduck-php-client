<?php

namespace Wildduck\Exceptions;

class InvalidRequestException extends \Exception implements \Throwable
{

    private $errors;

    public function __construct(array $errors)
    {
        $class = get_called_class();
        $this->errors = $errors;

        parent::__construct("Validation errors in $class", 0, null);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
