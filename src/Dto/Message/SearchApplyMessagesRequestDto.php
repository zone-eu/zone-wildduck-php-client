<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for searching and applying actions to messages
 */
readonly class SearchApplyMessagesRequestDto implements RequestDtoInterface
{
    /**
     * @param array<string, mixed> $action Define actions to take with matching messages
     */
    public function __construct(
        public array $action,
        public ?string $q = null,
        public ?string $mailbox = null,
        public ?string $id = null,
        public ?string $thread = null,
        public ?string $query = null,
        public ?string $datestart = null,
        public ?string $dateend = null,
        public ?string $from = null,
        public ?string $to = null,
        public ?string $subject = null,
        public ?int $minSize = null,
        public ?int $maxSize = null,
        public ?bool $attachments = null,
        public ?bool $flagged = null,
        public ?bool $unseen = null,
        public ?bool $seen = null,
        public ?string $includeHeaders = null,
        public ?bool $searchable = null,
    ) {
    }

    public function toArray(): array
    {
        $data = ['action' => $this->action];

        return array_merge($data, array_filter([
            'q' => $this->q,
            'mailbox' => $this->mailbox,
            'id' => $this->id,
            'thread' => $this->thread,
            'query' => $this->query,
            'datestart' => $this->datestart,
            'dateend' => $this->dateend,
            'from' => $this->from,
            'to' => $this->to,
            'subject' => $this->subject,
            'minSize' => $this->minSize,
            'maxSize' => $this->maxSize,
            'attachments' => $this->attachments,
            'flagged' => $this->flagged,
            'unseen' => $this->unseen,
            'seen' => $this->seen,
            'includeHeaders' => $this->includeHeaders,
            'searchable' => $this->searchable,
        ], fn($value) => $value !== null));
    }
}
