<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for updating an outbound message
 */
class UpdateOutboundMessageResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public bool $success,
        public string $queueId,
        public string $sendTime,
        public int $updated,
        public ?string $code = null,
    ) {}

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }
        if (!isset($data['queueId'])) {
            throw DtoValidationException::missingRequiredField('queueId', 'string');
        }
        if (!isset($data['sendTime'])) {
            throw DtoValidationException::missingRequiredField('sendTime', 'string');
        }
        if (!isset($data['updated'])) {
            throw DtoValidationException::missingRequiredField('updated', 'int');
        }

        return new self(
            success: $data['success'],
            queueId: $data['queueId'],
            sendTime: $data['sendTime'],
            updated: $data['updated'],
            code: $data['code'] ?? null,
        );
    }
}
