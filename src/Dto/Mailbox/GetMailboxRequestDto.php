<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Mailbox;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for getting mailbox information
 */
class GetMailboxRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?string $path = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'path' => $this->path,
        ], fn($value) => $value !== null);
    }
}
