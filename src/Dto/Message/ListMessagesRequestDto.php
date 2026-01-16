<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for listing messages in a mailbox
 */
class ListMessagesRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?bool $unseen = null,
        public ?bool $metaData = null,
        public ?bool $threadCounters = null,
        public ?int $limit = null,
        public ?string $order = null,
        public ?string $next = null,
        public ?string $previous = null,
        public string|bool|null $includeHeaders = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'unseen' => $this->unseen,
            'metaData' => $this->metaData,
            'threadCounters' => $this->threadCounters,
            'limit' => $this->limit,
            'order' => $this->order,
            'next' => $this->next,
            'previous' => $this->previous,
            'includeHeaders' => $this->includeHeaders,
        ], fn($value) => $value !== null);
    }
}
