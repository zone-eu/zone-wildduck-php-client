<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\RequestDtoInterface;

class SearchOrTermsDto implements RequestDtoInterface
{
    public function __construct(
        public ?string $query = null,
        public ?string $from = null,
        public ?string $to = null,
        public ?string $subject = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'query' => $this->query,
            'from' => $this->from,
            'to' => $this->to,
            'subject' => $this->subject,
        ], fn($value) => $value !== null);
    }
}

/**
 * Request DTO for searching messages
 */
class SearchMessagesRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?string $q = null,
        public ?string $query = null,
        public ?string $mailbox = null,
        public ?string $id = null,
        public ?string $thread = null,
        public ?string $from = null,
        public ?string $to = null,
        public ?SearchOrTermsDto $or = null,
        public ?string $subject = null,
        public ?bool $attachments = null,
        public ?bool $flagged = null,
        public ?bool $unseen = null,
        public ?bool $seen = null,
        public ?string $dateStart = null,
        public ?string $dateEnd = null,
        public ?int $minSize = null,
        public ?int $maxSize = null,
        public string|bool|null $includeHeaders = null,
        public ?bool $searchable = null,
        public ?bool $threadCounters = null,
        public ?int $limit = null,
        public ?string $order = null,
        public ?string $next = null,
        public ?string $previous = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'q' => $this->q,
            'query' => $this->query,
            'mailbox' => $this->mailbox,
            'id' => $this->id,
            'thread' => $this->thread,
            'from' => $this->from,
            'to' => $this->to,
            'or' => $this->or?->toArray(),
            'subject' => $this->subject,
            'attachments' => $this->attachments,
            'flagged' => $this->flagged,
            'unseen' => $this->unseen,
            'seen' => $this->seen,
            'dateStart' => $this->dateStart,
            'dateEnd' => $this->dateEnd,
            'minSize' => $this->minSize,
            'maxSize' => $this->maxSize,
            'includeHeaders' => $this->includeHeaders,
            'searchable' => $this->searchable,
            'threadCounters' => $this->threadCounters,
            'limit' => $this->limit,
            'order' => $this->order,
            'next' => $this->next,
            'previous' => $this->previous,
        ], fn($value) => $value !== null);
    }
}
