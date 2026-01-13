<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Mailbox;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Request DTO for creating a new mailbox
 */
readonly class CreateMailboxRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string $path,
        public bool $hidden = false,
        public int $retention = 0,
        public bool $encryptMessages = false,
    ) {}

    public function toArray(): array
    {
        return [
            'path' => $this->path,
            'hidden' => $this->hidden,
            'retention' => $this->retention,
            'encryptMessages' => $this->encryptMessages,
        ];
    }
}
