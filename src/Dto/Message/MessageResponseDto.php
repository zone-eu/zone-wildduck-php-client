<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Dto\Shared\AttachmentResponseDto;
use Zone\Wildduck\Dto\Shared\ContentTypeResponseDto;
use Zone\Wildduck\Dto\Shared\ListBimiResponseDto;
use Zone\Wildduck\Dto\Shared\EnvelopeResponseDto;
use Zone\Wildduck\Dto\Shared\FileResponseDto;
use Zone\Wildduck\Dto\Shared\MailingListResponseDto;
use Zone\Wildduck\Dto\Shared\MessageReferenceResponseDto;
use Zone\Wildduck\Dto\Shared\OutboundResponseDto;
use Zone\Wildduck\Dto\Shared\RecipientResponseDto;
use Zone\Wildduck\Dto\Shared\VerificationResultsResponseDto;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for message information
 */
readonly class MessageResponseDto implements ResponseDtoInterface
{
    /**
     * @param RecipientResponseDto[] $to
     * @param RecipientResponseDto[] $cc
     * @param RecipientResponseDto[] $bcc
     * @param RecipientResponseDto[] $replyTo
     * @param string[] $html
     * @param AttachmentResponseDto[] $attachments
     * @param FileResponseDto[] $files
     * @param OutboundResponseDto[] $outbound
     * @param string[] $forwardTargets
     */
    public function __construct(
        public bool $success = true,
        public int $id = 0,
        public string $mailbox = '',
        public string $user = '',
        public ?EnvelopeResponseDto $envelope = null,
        public string $thread = '',
        public ?RecipientResponseDto $from = null,
        public string $subject = '',
        public string $messageId = '',
        public string $date = '',
        public int $size = 0,
        public bool $seen = false,
        public bool $deleted = false,
        public bool $flagged = false,
        public bool $draft = false,
        public bool $answered = false,
        public bool $forwarded = false,
        public ?ContentTypeResponseDto $contentType = null,
        public ?array $replyTo = null,
        public ?array $to = null,
        public ?array $cc = null,
        public ?array $bcc = null,
        public ?string $idate = null,
        public ?MailingListResponseDto $list = null,
        public ?string $expires = null,
        /** @var string[]|null */ public ?array $html = null,
        public ?string $text = null,
        public ?array $attachments = null,
        public ?VerificationResultsResponseDto $verificationResults = null,
        public ?ListBimiResponseDto $bimi = null,
        /** @var array<string, mixed>|null Custom metadata */ public mixed $metaData = null,
        public ?MessageReferenceResponseDto $references = null,
        public ?array $files = null,
        public ?array $outbound = null,
        /** @var string[]|null */ public ?array $forwardTargets = null,
        public ?MessageReferenceResponseDto $reference = null,
        public ?bool $encrypted = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        // Make all fields optional since list results have different structure than single message get
        $envelope = null;
        if (isset($data['envelope']) && is_array($data['envelope'])) {
            $envelope = EnvelopeResponseDto::fromArray($data['envelope']);
        }

        $from = null;
        if (isset($data['from']) && is_array($data['from'])) {
            $from = RecipientResponseDto::fromArray($data['from']);
        }

        $contentType = null;
        if (isset($data['contentType']) && is_array($data['contentType'])) {
            $contentType = ContentTypeResponseDto::fromArray($data['contentType']);
        }

        // Parse recipient arrays
        $to = isset($data['to']) && is_array($data['to']) ? array_map(fn($r) => is_array($r) ? RecipientResponseDto::fromArray($r) : null, $data['to']) : null;
        $cc = isset($data['cc']) && is_array($data['cc']) ? array_map(fn($r) => is_array($r) ? RecipientResponseDto::fromArray($r) : null, $data['cc']) : null;
        $bcc = isset($data['bcc']) && is_array($data['bcc']) ? array_map(fn($r) => is_array($r) ? RecipientResponseDto::fromArray($r) : null, $data['bcc']) : null;
        $replyTo = isset($data['replyTo']) && is_array($data['replyTo']) ? array_map(fn($r) => is_array($r) ? RecipientResponseDto::fromArray($r) : null, $data['replyTo']) : null;

        // Parse attachments
        $attachments = null;
        if (isset($data['attachments']) && is_array($data['attachments'])) {
            $attachments = array_map(fn($a) => is_array($a) ? AttachmentResponseDto::fromArray($a) : null, $data['attachments']);
        }

        // Parse files
        $files = null;
        if (isset($data['files']) && is_array($data['files'])) {
            $files = array_map(fn($f) => is_array($f) ? FileResponseDto::fromArray($f) : null, $data['files']);
        }

        // Parse outbound
        $outbound = null;
        if (isset($data['outbound']) && is_array($data['outbound'])) {
            $outbound = array_map(fn($o) => is_array($o) ? OutboundResponseDto::fromArray($o) : null, $data['outbound']);
        }

        // Parse mailing list
        $list = isset($data['list']) && is_array($data['list']) ? MailingListResponseDto::fromArray($data['list']) : null;

        // Parse verification results
        $verificationResults = isset($data['verificationResults']) && is_array($data['verificationResults']) ? VerificationResultsResponseDto::fromArray($data['verificationResults']) : null;

        // Parse BIMI
        $bimi = isset($data['bimi']) && is_array($data['bimi']) ? ListBimiResponseDto::fromArray($data['bimi']) : null;

        // Parse references
        $references = isset($data['references']) && is_array($data['references']) ? MessageReferenceResponseDto::fromArray($data['references']) : null;
        $reference = isset($data['reference']) && is_array($data['reference']) ? MessageReferenceResponseDto::fromArray($data['reference']) : null;

        return new self(
            success: $data['success'] ?? true,
            id: $data['id'] ?? 0,
            mailbox: $data['mailbox'] ?? '',
            user: $data['user'] ?? '',
            envelope: $envelope,
            thread: $data['thread'] ?? '',
            from: $from,
            subject: $data['subject'] ?? '',
            messageId: $data['messageId'] ?? '',
            date: $data['date'] ?? '',
            size: $data['size'] ?? 0,
            seen: $data['seen'] ?? false,
            deleted: $data['deleted'] ?? false,
            flagged: $data['flagged'] ?? false,
            draft: $data['draft'] ?? false,
            answered: $data['answered'] ?? false,
            forwarded: $data['forwarded'] ?? false,
            contentType: $contentType,
            replyTo: $replyTo,
            to: $to,
            cc: $cc,
            bcc: $bcc,
            idate: $data['idate'] ?? null,
            list: $list,
            expires: $data['expires'] ?? null,
            html: isset($data['html']) && is_array($data['html']) ? $data['html'] : null,
            text: $data['text'] ?? null,
            attachments: $attachments,
            verificationResults: $verificationResults,
            bimi: $bimi,
            metaData: $data['metaData'] ?? null,
            references: $references,
            files: $files,
            outbound: $outbound,
            forwardTargets: isset($data['forwardTargets']) && is_array($data['forwardTargets']) ? $data['forwardTargets'] : null,
            reference: $reference,
            encrypted: $data['encrypted'] ?? null,
        );
    }
}
