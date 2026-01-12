<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Mailbox;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Generic response DTO for resource creation
 * Used by most create endpoints that return {success: bool}
 */
readonly class DeleteAllMessagesResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public bool $success,
        public int $deleted,
        public int $errors,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }
        if (!isset($data['deleted'])) {
            throw DtoValidationException::missingRequiredField('deleted', 'int');
        }
        if (!isset($data['errors'])) {
            throw DtoValidationException::missingRequiredField('errors', 'int');
        }

        return new self(
            success: $data['success'],
            deleted: $data['deleted'],
            errors: $data['errors'],
        );
    }
}
