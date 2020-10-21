<?php

namespace Zone\Wildduck\Exception;

class ValidationException extends WildduckException
{

    private string $errors;

    public function __construct(string $errors = '')
    {
        $this->errors = $this->formatErrors($errors);
        parent::__construct('Request failed due to validation error', 422);
    }

    public function getErrors(): string // TODO: Currently Wildduck only returns all validation errors as one string
    {
        return $this->errors;
    }

    private function formatErrors($errors): string
    {
        // TODO
        return $errors;
    }
}