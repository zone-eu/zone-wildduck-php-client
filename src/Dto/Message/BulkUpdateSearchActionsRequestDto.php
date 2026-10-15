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
        /** @var string[]|null Custom labels to set on matched messages. Replaces all existing custom labels. */
        public ?array $labels = null,
        /** @var string[]|null Custom labels to add to matched messages without affecting existing labels. */
        public ?array $addLabels = null,
        /** @var string[]|null Custom labels to remove from matched messages without affecting other labels. */
        public ?array $removeLabels = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'moveTo' => $this->moveTo,
            'seen' => $this->seen,
            'flagged' => $this->flagged,
            'delete' => $this->delete,
            'labels' => $this->labels,
            'addLabels' => $this->addLabels,
            'removeLabels' => $this->removeLabels,
        ], fn($value) => $value !== null);
    }
}
