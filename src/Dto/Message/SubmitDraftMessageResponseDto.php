<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for submit draft message operation
 */
readonly class SubmitDraftMessageResponseDto implements ResponseDtoInterface
{
    /**
     * @param array{ id: int, mailbox: string, size: int } $message
     */
    public function __construct(
        public bool $success,
        public ?string $queueId = null,
        public ?array $message = null
    ) {}

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }

        return new self(
            success: $data['success'],
            queueId: $data['queueId'] ?? null,
            message: $data['message'] ?? null,
        );
    }
}
