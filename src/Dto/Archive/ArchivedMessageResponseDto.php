<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Archive;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Dto\Shared\BimiResponseDto;
use Zone\Wildduck\Dto\Shared\ContentTypeResponseDto;
use Zone\Wildduck\Dto\Shared\ListBimiResponseDto;
use Zone\Wildduck\Dto\Shared\MessageReferenceResponseDto;
use Zone\Wildduck\Dto\Shared\RecipientResponseDto;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for archived message information
 */
readonly class ArchivedMessageResponseDto implements ResponseDtoInterface
{
    /**
     * @param RecipientResponseDto[] $to
     * @param RecipientResponseDto[] $cc
     * @param RecipientResponseDto[] $bcc
     * @param MessageReferenceResponseDto[] $references
     * @param array<string, mixed>|null $headers
     */
    public function __construct(
        public int|string $id,
        public string $mailbox,
        public string $thread,
        public RecipientResponseDto $from,
        public array $to,
        public array $cc,
        public array $bcc,
        public string $messageId,
        public string $subject,
        public string $date,
        public string $intro,
        public int $size,
        public bool $attachments,
        public bool $seen,
        public bool $deleted,
        public bool $flagged,
        public bool $draft,
        public bool $answered,
        public bool $forwarded,
        public array $references,
        public ?ListBimiResponseDto $bimi,
        public ContentTypeResponseDto $contentType,
        public ?int $threadMessageCount = null,
        public ?string $idate = null,
        public ?bool $encrypted = null,
        public ?array $metaData = null,
        public ?array $headers = null,
    ) {}

    public static function fromArray(array $data): self
    {
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'string');
        }
        if (!isset($data['mailbox'])) {
            throw DtoValidationException::missingRequiredField('mailbox', 'string');
        }
        if (!isset($data['thread'])) {
            throw DtoValidationException::missingRequiredField('thread', 'string');
        }
        if (!isset($data['from'])) {
            throw DtoValidationException::missingRequiredField('from', 'array');
        }
        if (!isset($data['subject'])) {
            throw DtoValidationException::missingRequiredField('subject', 'string');
        }
        if (!isset($data['date'])) {
            throw DtoValidationException::missingRequiredField('date', 'string');
        }

        $from = is_array($data['from']) ? RecipientResponseDto::fromArray($data['from']) : new RecipientResponseDto('', '');
        $to = [];
        if (isset($data['to']) && is_array($data['to'])) {
            $to = array_map(fn($r) => is_array($r) ? RecipientResponseDto::fromArray($r) : null, $data['to']);
            $to = array_filter($to);
        }

        $cc = [];
        if (isset($data['cc']) && is_array($data['cc'])) {
            $cc = array_map(fn($r) => is_array($r) ? RecipientResponseDto::fromArray($r) : null, $data['cc']);
            $cc = array_filter($cc);
        }

        $bcc = [];
        if (isset($data['bcc']) && is_array($data['bcc'])) {
            $bcc = array_map(fn($r) => is_array($r) ? RecipientResponseDto::fromArray($r) : null, $data['bcc']);
            $bcc = array_filter($bcc);
        }

        $references = [];
        if (isset($data['references']) && is_array($data['references'])) {
            $references = array_map(fn($r) => is_array($r) ? MessageReferenceResponseDto::fromArray($r) : null, $data['references']);
            $references = array_filter($references);
        }

        $bimi = isset($data['bimi']) && is_array($data['bimi']) ? ListBimiResponseDto::fromArray($data['bimi']) : null;
        $contentType = isset($data['contentType']) && is_array($data['contentType']) ? ContentTypeResponseDto::fromArray($data['contentType']) : new ContentTypeResponseDto('text/plain');

        return new self(
            id: $data['id'],
            mailbox: $data['mailbox'],
            thread: $data['thread'],
            from: $from,
            to: $to,
            cc: $cc,
            bcc: $bcc,
            messageId: $data['messageId'] ?? '',
            subject: $data['subject'],
            date: $data['date'],
            intro: $data['intro'] ?? '',
            size: $data['size'] ?? 0,
            attachments: $data['attachments'] ?? false,
            seen: $data['seen'] ?? false,
            deleted: $data['deleted'] ?? false,
            flagged: $data['flagged'] ?? false,
            draft: $data['draft'] ?? false,
            answered: $data['answered'] ?? false,
            forwarded: $data['forwarded'] ?? false,
            references: $references,
            bimi: $bimi,
            contentType: $contentType,
            threadMessageCount: $data['threadMessageCount'] ?? null,
            idate: $data['idate'] ?? null,
            encrypted: $data['encrypted'] ?? null,
            metaData: isset($data['metaData']) && is_array($data['metaData']) ? $data['metaData'] : null,
            headers: isset($data['headers']) && is_array($data['headers']) ? $data['headers'] : null,
        );
    }
}
