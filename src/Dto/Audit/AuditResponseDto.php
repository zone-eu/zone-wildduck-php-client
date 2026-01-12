<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Audit;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Audit DTO
 */
readonly class AuditResponseDto implements ResponseDtoInterface
{
    /**
     * @param array<string, mixed>|null $import Import status information
     */
    public function __construct(
        public string $id,
        public string $user,
        public string $expires,
        public ?array $import = null,
        public string|bool|null $start = null,
        public string|bool|null $end = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'string');
        }
        if (!isset($data['user'])) {
            throw DtoValidationException::missingRequiredField('user', 'string');
        }
        if (!isset($data['expires'])) {
            throw DtoValidationException::missingRequiredField('expires', 'string');
        }

        if (!is_string($data['id'])) {
            throw DtoValidationException::invalidType('id', 'string', $data['id']);
        }
        if (!is_string($data['user'])) {
            throw DtoValidationException::invalidType('user', 'string', $data['user']);
        }
        if (!is_string($data['expires'])) {
            throw DtoValidationException::invalidType('expires', 'string', $data['expires']);
        }

        return new self(
            id: $data['id'],
            user: $data['user'],
            expires: $data['expires'],
            import: isset($data['import']) && is_array($data['import']) ? $data['import'] : null,
            start: $data['start'] ?? null,
            end: $data['end'] ?? null,
        );
    }
}
