<?php

namespace Zone\Wildduck\Exception;

use AllowDynamicProperties;

#[AllowDynamicProperties]
class InvalidAccessTokenException extends WildduckException
{
    public function __construct(string $message)
    {
	    if ($message === ''){
		    $message = 'Internal Server Error';
	    }

        parent::__construct($message);
    }
}
