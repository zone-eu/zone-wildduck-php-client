<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Generic response DTO for resource creation
 * Used by most create endpoints that return {success: bool}
 */
readonly class SuccessResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public bool $success,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }

        return new self(
            success: $data['success'],
        );
    }
}
