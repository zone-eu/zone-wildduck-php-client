<?php

namespace Zone\Wildduck\Exception;

use AllowDynamicProperties;
use Exception;

#[AllowDynamicProperties]
class ApiConnectionException extends Exception
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
