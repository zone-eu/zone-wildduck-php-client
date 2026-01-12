<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\RequestDtoInterface;

readonly class SearchTermsDto implements RequestDtoInterface
{
    public ?string $query;
    public ?string $dateStart;
    public ?string $dateEnd;
    public ?string $from;
    public ?string $to;
    public ?string $subject;
    public ?int $minSize;
    public ?int $maxSize;
    public ?bool $attachments;
    public ?bool $flagged;
    public ?bool $unseen;
    public ?bool $seen;

    public function __construct(
        ?string $query = null,
        ?string $dateStart = null,
        ?string $dateEnd = null,
        ?string $from = null,
        ?string $to = null,
        ?string $subject = null,
        ?int $minSize = null,
        ?int $maxSize = null,
        ?bool $attachments = null,
        ?bool $flagged = null,
        ?bool $unseen = null,
        ?bool $seen = null,
    ) {
        $this->query = $query;
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
        $this->from = $from;
        $this->to = $to;
        $this->subject = $subject;
        $this->minSize = $minSize;
        $this->maxSize = $maxSize;
        $this->attachments = $attachments;
        $this->flagged = $flagged;
        $this->unseen = $unseen;
        $this->seen = $seen;
    }

    public function toArray(): array
    {
        return array_filter([
            'query' => $this->query,
            'dateStart' => $this->dateStart,
            'dateEnd' => $this->dateEnd,
            'from' => $this->from,
            'to' => $this->to,
            'subject' => $this->subject,
            'minSize' => $this->minSize,
            'maxSize' => $this->maxSize,
            'attachments' => $this->attachments,
            'flagged' => $this->flagged,
            'unseen' => $this->unseen,
            'seen' => $this->seen,
        ], fn($value) => $value !== null);
    }
}

/**
 * Request DTO for searching messages
 */
readonly class SearchMessagesRequestDto extends SearchTermsDto
{
    public function __construct(
        public ?string $q = null,
        public ?string $mailbox = null,
        public ?string $id = null,
        public ?string $thread = null,
        public ?SearchTermsDto $or = null,
        public string|bool|null $includeHeaders = null,
        public ?bool $searchable = null,
        public ?bool $threadCounters = null,
        public ?int $limit = null,
        public ?string $order = null,
        public ?string $next = null,
        public ?string $previous = null,
        ?string $query = null,
        ?string $dateStart = null,
        ?string $dateEnd = null,
        ?string $from = null,
        ?string $to = null,
        ?string $subject = null,
        ?int $minSize = null,
        ?int $maxSize = null,
        ?bool $attachments = null,
        ?bool $flagged = null,
        ?bool $unseen = null,
        ?bool $seen = null,
    ) {
        parent::__construct(
            query: $query,
            dateStart: $dateStart,
            dateEnd: $dateEnd,
            from: $from,
            to: $to,
            subject: $subject,
            minSize: $minSize,
            maxSize: $maxSize,
            attachments: $attachments,
            flagged: $flagged,
            unseen: $unseen,
            seen: $seen,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'q' => $this->q,
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
