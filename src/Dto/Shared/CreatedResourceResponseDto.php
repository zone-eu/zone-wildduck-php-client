<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Generic response DTO for resource creation
 * Used by most create endpoints that return {success: bool, id: string}
 */
readonly class CreatedResourceResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public bool $success,
        public string $id,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'string');
        }

        return new self(
            success: $data['success'],
            id: $data['id'],
        );
    }
}
