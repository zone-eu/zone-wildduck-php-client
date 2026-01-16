<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Submission;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Dto\Shared\RecipientRequestDto;
use Zone\Wildduck\Dto\Shared\HeaderRequestDto;
use Zone\Wildduck\Dto\Shared\AttachmentReferenceRequestDto;
use Zone\Wildduck\Dto\Shared\MessageReferenceRequestDto;
use Zone\Wildduck\Dto\Shared\EnvelopeRequestDto;

/**
 * Request DTO for submitting a message for delivery
 */
class SubmitMessageRequestDto implements RequestDtoInterface
{
    /**
     * @param RecipientRequestDto[]|null $to
     * @param RecipientRequestDto[]|null $cc
     * @param RecipientRequestDto[]|null $bcc
     * @param RecipientRequestDto[]|null $replyTo
     * @param HeaderRequestDto[]|null $headers
     * @param AttachmentReferenceRequestDto[]|null $attachments
     * @param array<string, mixed>|null $meta
     */
    public function __construct(
        public ?string $mailbox = null,
        public ?RecipientRequestDto $from = null,
        public ?array $replyTo = null,
        public ?array $to = null,
        public ?array $cc = null,
        public ?array $bcc = null,
        public ?array $headers = null,
        public ?string $subject = null,
        public ?string $text = null,
        public ?string $html = null,
        public ?array $attachments = null,
        public ?array $meta = null,
        public ?MessageReferenceRequestDto $reference = null,
        public bool $isDraft = false,
        public ?MessageReferenceRequestDto $draft = null,
        public ?string $sendTime = null,
        public bool $uploadOnly = false,
        public ?EnvelopeRequestDto $envelope = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'isDraft' => $this->isDraft,
            'uploadOnly' => $this->uploadOnly,
        ];

        if ($this->mailbox !== null) {
            $data['mailbox'] = $this->mailbox;
        }
        if ($this->from !== null) {
            $data['from'] = $this->from->toArray();
        }
        if ($this->replyTo !== null) {
            $data['replyTo'] = array_map(fn($r) => $r->toArray(), $this->replyTo);
        }
        if ($this->to !== null) {
            $data['to'] = array_map(fn($r) => $r->toArray(), $this->to);
        }
        if ($this->cc !== null) {
            $data['cc'] = array_map(fn($r) => $r->toArray(), $this->cc);
        }
        if ($this->bcc !== null) {
            $data['bcc'] = array_map(fn($r) => $r->toArray(), $this->bcc);
        }
        if ($this->headers !== null) {
            $data['headers'] = array_map(fn($h) => $h->toArray(), $this->headers);
        }
        if ($this->subject !== null) {
            $data['subject'] = $this->subject;
        }
        if ($this->text !== null) {
            $data['text'] = $this->text;
        }
        if ($this->html !== null) {
            $data['html'] = $this->html;
        }
        if ($this->attachments !== null) {
            $data['attachments'] = array_map(fn($a) => $a->toArray(), $this->attachments);
        }
        if ($this->meta !== null) {
            $data['meta'] = $this->meta;
        }
        if ($this->reference !== null) {
            $data['reference'] = $this->reference->toArray();
        }
        if ($this->draft !== null) {
            $data['draft'] = $this->draft->toArray();
        }
        if ($this->sendTime !== null) {
            $data['sendTime'] = $this->sendTime;
        }
        if ($this->envelope !== null) {
            $data['envelope'] = $this->envelope->toArray();
        }

        return $data;
    }
}
