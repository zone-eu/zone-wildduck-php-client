<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * User limits DTO containing quota information for various operations
 */
readonly class UserLimitsResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public ?QuotaResponseDto $quota = null,
        public ?QuotaResponseDto $recipients = null,
        public ?QuotaResponseDto $forwards = null,
        public ?QuotaResponseDto $received = null,
        public ?QuotaResponseDto $imapUpload = null,
        public ?QuotaResponseDto $imapDownload = null,
        public ?QuotaResponseDto $pop3Download = null,
        public ?QuotaResponseDto $imapConnections = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            quota: isset($data['quota']) ? QuotaResponseDto::fromArray($data['quota']) : null,
            recipients: isset($data['recipients']) ? QuotaResponseDto::fromArray($data['recipients']) : null,
            forwards: isset($data['forwards']) ? QuotaResponseDto::fromArray($data['forwards']) : null,
            received: isset($data['received']) ? QuotaResponseDto::fromArray($data['received']) : null,
            imapUpload: isset($data['imapUpload']) ? QuotaResponseDto::fromArray($data['imapUpload']) : null,
            imapDownload: isset($data['imapDownload']) ? QuotaResponseDto::fromArray($data['imapDownload']) : null,
            pop3Download: isset($data['pop3Download']) ? QuotaResponseDto::fromArray($data['pop3Download']) : null,
            imapConnections: isset($data['imapConnections']) ? QuotaResponseDto::fromArray($data['imapConnections']) : null,
        );
    }
}
