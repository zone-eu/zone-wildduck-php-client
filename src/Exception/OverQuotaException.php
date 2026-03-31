<?php

namespace Zone\Wildduck\Exception;

use AllowDynamicProperties;

#[AllowDynamicProperties]
class OverQuotaException extends WildduckException
{
    /**
     * @param string $message
     */
    public function __construct(string $message = '', int $code = 413)
    {
        if ($message === '') {
            $message = 'Over Quota Error';
        }

        parent::__construct($message, $code);
    }
}
