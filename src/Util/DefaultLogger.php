<?php

namespace Zone\Wildduck\Util;

use AllowDynamicProperties;
use BadMethodCallException;
use Override;

/**
 * A very basic implementation of LoggerInterface that has just enough
 * functionality that it can be the default for this library.
 */
#[AllowDynamicProperties]
class DefaultLogger implements LoggerInterface
{
    /** @var int */
    public int $messageType = 0;

    /** @var null|string */
    public string|null $destination = null;

    #[Override]
    public function error(string $message, array $context = []): void
    {
        if ($context !== []) {
            throw new BadMethodCallException('DefaultLogger does not currently implement context. Please implement if you need it.');
        }

        if (null === $this->destination) {
            error_log($message, 0);
        } else {
            error_log($message, 3, $this->destination);
        }
    }
}
