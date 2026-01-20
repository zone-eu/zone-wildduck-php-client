<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Mailbox;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for deleting all messages in a mailbox
 * If in the request async: true was specified, the response will contain scheduled timestamp and existing flag, else deleted and errors count
 */
readonly class DeleteAllMessagesResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public bool $success,
        public ?bool $existing = null,
        public ?string $scheduled = null,
        public ?int $deleted = null,
        public ?int $errors = null,
    ) {}

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }

        return new self(
            success: $data['success'],
            existing: $data['existing'] ?? null,
            scheduled: $data['scheduled'] ?? null,
            deleted: $data['deleted'] ?? null,
            errors: $data['errors'] ?? null,
        );
    }
}
