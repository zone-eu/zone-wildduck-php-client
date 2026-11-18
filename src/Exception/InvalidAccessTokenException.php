<?php

namespace Zone\Wildduck\Exception;

use AllowDynamicProperties;

#[AllowDynamicProperties]
class InvalidAccessTokenException extends WildduckException
{
    public function __construct(string $message, ?int $httpStatus = null)
    {
        if ($message === '') {
            $message = 'Internal Server Error';
        }

        parent::__construct($message, 0, null, $httpStatus);
    }
}
