<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Dkim;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for DKIM key information
 */
readonly class DkimResolveResponseDto implements ResponseDtoInterface
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
