<?php

namespace Zone\Wildduck\Exception;

use AllowDynamicProperties;

#[AllowDynamicProperties]
class MissingGlobalAccessTokenException extends WildduckException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
