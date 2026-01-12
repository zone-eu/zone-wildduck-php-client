<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Audit;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Audit DTO
 */
readonly class CreateAuditResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public string $id,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'string');
        }

        if (!is_string($data['id'])) {
            throw DtoValidationException::invalidType('id', 'string', $data['id']);
        }

        return new self(
            id: $data['id'],
        );
    }
}
