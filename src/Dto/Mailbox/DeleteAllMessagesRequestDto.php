<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Mailbox;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for deleting all messages in a mailbox
 */
class DeleteAllMessagesRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?bool $async = null,
        public ?bool $skipArchive = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'async' => $this->async,
            'skipArchive' => $this->skipArchive,
        ], fn($value) => $value !== null);
    }
}
