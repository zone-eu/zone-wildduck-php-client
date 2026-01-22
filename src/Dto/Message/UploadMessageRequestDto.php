<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Dto\Shared\AttachmentReferenceRequestDto;
use Zone\Wildduck\Dto\Shared\HeaderRequestDto;
use Zone\Wildduck\Dto\Shared\ListBimiResponseDto;
use Zone\Wildduck\Dto\Shared\MessageReferenceRequestDto;
use Zone\Wildduck\Dto\Shared\RecipientRequestDto;
use Zone\Wildduck\Dto\Traits\MetaDataSupportTrait;

/**
 * Request DTO for uploading/creating a message
 */
class UploadMessageRequestDto implements RequestDtoInterface
{
    use MetaDataSupportTrait;

    /**
     * @param RecipientRequestDto|null $from
     * @param RecipientRequestDto[]|null $replyTo
     * @param RecipientRequestDto[]|null $to
     * @param RecipientRequestDto[]|null $cc
     * @param RecipientRequestDto[]|null $bcc
     * @param HeaderRequestDto[]|null $headers
     * @param string[]|null $files
     * @param AttachmentReferenceRequestDto[]|null $attachments
     * @param array<string, mixed>|null $metaData
     */
    public function __construct(
        public ?string $date = null,
        public bool $unseen = false,
        public bool $flagged = false,
        public bool $draft = false,
        public ?string $raw = null,
        public ?RecipientRequestDto $from = null,
        public ?array $replyTo = null,
        public ?array $to = null,
        public ?array $cc = null,
        public ?array $bcc = null,
        public ?array $headers = null,
        public ?string $subject = null,
        public ?string $text = null,
        public ?string $html = null,
        public ?array $files = null,
        public ?array $attachments = null,
        /** @var array<string, mixed>|null Custom metadata */
        public ?array $metaData = null,
        public ?MessageReferenceRequestDto $reference = null,
        /** @var array{mailbox: string, id: int}|null */
        public ?array $replacePrevious = null,
        public ?ListBimiResponseDto $bimi = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'unseen' => $this->unseen,
            'flagged' => $this->flagged,
            'draft' => $this->draft,
        ];

        if ($this->date !== null) {
            $data['date'] = $this->date;
        }
        if ($this->raw !== null) {
            $data['raw'] = $this->raw;
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
        if ($this->files !== null) {
            $data['files'] = $this->files;
        }
        if ($this->attachments !== null) {
            $data['attachments'] = array_map(fn($a) => $a->toArray(), $this->attachments);
        }
        if ($this->reference !== null) {
            $data['reference'] = $this->reference->toArray();
        }
        if ($this->replacePrevious !== null) {
            $data['replacePrevious'] = $this->replacePrevious;
        }
        if ($this->bimi !== null) {
            $data['bimi'] = $this->bimi->toArray();
        }

        return array_merge($data, $this->getMetaDataArray());
    }
}
