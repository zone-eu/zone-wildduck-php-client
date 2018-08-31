<?php

namespace Wildduck\Exceptions;

use Illuminate\Validation\Validator;

class InvalidRequestException extends \Exception
{

    private $errors;

    public function __construct($errors, $code = 0, \Throwable $previous = null)
    {
        $class = parent::getTrace()[0]['class'];
        if ($errors instanceof Validator) {
            $this->errors = $errors->errors()->toArray();
        } else {
            $this->errors = is_array($errors) ? $errors : [$errors];
        }

        parent::__construct("Validation errors in $class", $code, $previous);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
