<?php

namespace Zone\Wildduck\Exception;

use AllowDynamicProperties;
use Exception;

#[AllowDynamicProperties]
class WildduckException extends Exception
{
    /**
     * Machine-readable WildDuck error code when the subclass maps one, otherwise null
     */
    public function getErrorCode(): ?string
    {
        return null;
    }
}
