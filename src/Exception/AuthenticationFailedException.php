<?php

namespace Zone\Wildduck\Exception;

use AllowDynamicProperties;

#[AllowDynamicProperties]
class AuthenticationFailedException extends WildduckException
{
    /**
     * @param string $message
     * @param int $code 100='error.authentication'
     */
    public function __construct(string $message = '', int $code = 100)
    {
        if ($message === '') {
            $message = 'Internal Server Error';
        }

        parent::__construct($message, $code);
    }
}
