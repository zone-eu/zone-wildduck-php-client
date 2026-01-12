<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for upload message operation
 */
readonly class UploadMessageResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public bool $success,
        public UploadMessageResponseMessageDto $message,
        public ?bool $previousDeleted = null,
        public ?string $previousDeleteError = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }
        if (!isset($data['message'])) {
            throw DtoValidationException::missingRequiredField('message', 'object');
        }

        return new self(
            success: $data['success'],
            message: UploadMessageResponseMessageDto::fromArray($data['message']),
            previousDeleted: $data['previousDeleted'] ?? null,
            previousDeleteError: $data['previousDeleteError'] ?? null,
        );
    }
}
