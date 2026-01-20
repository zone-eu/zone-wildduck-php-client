<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for listing messages in a mailbox
 */
class MessageGetRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?bool $replaceCidLinks = null,
        public ?bool $markAsSeen = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'replaceCidLinks' => $this->replaceCidLinks,
            'markAsSeen' => $this->markAsSeen,
        ], fn($value) => $value !== null);
    }
}
