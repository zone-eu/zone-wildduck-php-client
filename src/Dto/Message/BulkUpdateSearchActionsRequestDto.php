<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for bulk updating messages
 */
class BulkUpdateSearchActionsRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?string $moveTo = null,
        public ?bool $seen = null,
        public ?bool $flagged = null,
        public ?bool $delete = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'moveTo' => $this->moveTo,
            'seen' => $this->seen,
            'flagged' => $this->flagged,
            'delete' => $this->delete,
        ], fn($value) => $value !== null);
    }
}
