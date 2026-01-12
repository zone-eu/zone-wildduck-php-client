<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Mailbox;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for updating mailbox information
 */
readonly class UpdateMailboxRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?string $path = null,
        public ?int $retention = null,
        public ?bool $subscribed = null,
        public ?bool $encryptMessages = null,
        public ?bool $hidden = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'path' => $this->path,
            'retention' => $this->retention,
            'subscribed' => $this->subscribed,
            'encryptMessages' => $this->encryptMessages,
            'hidden' => $this->hidden,
        ], fn($value) => $value !== null);
    }
}
