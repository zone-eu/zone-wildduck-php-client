<?php

namespace Wildduck\Exceptions;

use Illuminate\Validation\Validator;

class InvalidRequestException extends \Exception
{

    private $errors;

    public function __construct(Validator $validator, $code = 0, \Throwable $previous = null)
    {
        $class = parent::getTrace()[0]['class'];
        $this->errors = $validator->errors()->toArray();

        parent::__construct("Validation errors in $class", $code, $previous);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
