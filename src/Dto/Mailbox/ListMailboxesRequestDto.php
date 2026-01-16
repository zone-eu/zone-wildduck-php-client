<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Mailbox;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for listing mailboxes
 */
class ListMailboxesRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?bool $specialUse = null,
        public ?bool $showHidden = null,
        public ?bool $counters = null,
        public ?bool $sizes = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'specialUse' => $this->specialUse,
            'showHidden' => $this->showHidden,
            'counters' => $this->counters,
            'sizes' => $this->sizes,
        ], fn($value) => $value !== null);
    }
}
