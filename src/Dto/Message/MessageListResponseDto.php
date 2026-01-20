<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Dto\Shared\ContentTypeResponseDto;
use Zone\Wildduck\Dto\Shared\ListBimiResponseDto;
use Zone\Wildduck\Dto\Shared\MessageReferenceResponseDto;
use Zone\Wildduck\Dto\Shared\RecipientResponseDto;

/**
 * Response DTO for message information
 */
readonly class MessageListResponseDto implements ResponseDtoInterface
{
    /**
     * @param RecipientResponseDto[] $to
     * @param RecipientResponseDto[] $cc
     * @param RecipientResponseDto[] $bcc
     */
    public function __construct(
        public int $id,
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
        public bool $attachments,
        public int $size,
        public bool $seen,
        public bool $deleted,
        public bool $flagged,
        public bool $draft,
        public bool $answered,
        public bool $forwarded,
        public MessageReferenceResponseDto $references,
        public ContentTypeResponseDto $contentType,
        public ?ListBimiResponseDto $bimi = null,
        public ?int $threadMessageCount = null,
        public ?string $idate = null,
        public ?bool $encrypted = null,
        /** @var array<string, mixed>|null Custom metadata */
        public mixed $metaData = null,
    ) {}
    public static function fromArray(array $data): self
    {
        // From
        $from = RecipientResponseDto::fromArray($data['from'] ?? []);

        // Recipients
        $to = [];
        if (isset($data['to']) && is_array($data['to'])) {
            foreach ($data['to'] as $r) {
                if (is_array($r)) {
                    $to[] = RecipientResponseDto::fromArray($r);
                }
            }
        }

        $cc = [];
        if (isset($data['cc']) && is_array($data['cc'])) {
            foreach ($data['cc'] as $r) {
                if (is_array($r)) {
                    $cc[] = RecipientResponseDto::fromArray($r);
                }
            }
        }

        $bcc = [];
        if (isset($data['bcc']) && is_array($data['bcc'])) {
            foreach ($data['bcc'] as $r) {
                if (is_array($r)) {
                    $bcc[] = RecipientResponseDto::fromArray($r);
                }
            }
        }

        // Content type
        $contentType = ContentTypeResponseDto::fromArray($data['contentType'] ?? []);

        // References
        $references = MessageReferenceResponseDto::fromArray($data['references'] ?? []);

        // BIMI
        $bimi = isset($data['bimi']) ? ListBimiResponseDto::fromArray($data['bimi']) : null;

        // Flags / simple fields
        $intro = $data['intro'] ?? '';
        $attachmentsFlag = isset($data['attachments']) && is_array($data['attachments']) && count($data['attachments']) > 0;
        $size = isset($data['size']) ? (int) $data['size'] : 0;
        $seen = (bool) ($data['seen'] ?? false);
        $deleted = (bool) ($data['deleted'] ?? false);
        $flagged = (bool) ($data['flagged'] ?? false);
        $draft = (bool) ($data['draft'] ?? false);
        $answered = (bool) ($data['answered'] ?? false);
        $forwarded = (bool) ($data['forwarded'] ?? false);

        $threadMessageCount = isset($data['threadMessageCount']) ? (int) $data['threadMessageCount'] : null;
        $idate = $data['idate'] ?? null;
        $encrypted = isset($data['encrypted']) ? (bool) $data['encrypted'] : null;
        $metaData = $data['metaData'] ?? null;

        return new self(
            isset($data['id']) ? (int) $data['id'] : 0,
            $data['mailbox'] ?? '',
            $data['thread'] ?? '',
            $from,
            $to,
            $cc,
            $bcc,
            $data['messageId'] ?? '',
            $data['subject'] ?? '',
            $data['date'] ?? '',
            $intro,
            $attachmentsFlag,
            $size,
            $seen,
            $deleted,
            $flagged,
            $draft,
            $answered,
            $forwarded,
            $references,
            $bimi,
            $contentType,
            $threadMessageCount,
            $idate,
            $encrypted,
            $metaData,
        );
    }
}
