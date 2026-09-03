<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\McpToken;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * MCP token metadata DTO
 */
readonly class McpTokenResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public string $id,
        public string $description,
        public string $role,
        public string $audience,
        public string $created,
        public ?string $expires = null,
        public ?string $lastUse = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'string');
        }
        if (!isset($data['description'])) {
            throw DtoValidationException::missingRequiredField('description', 'string');
        }
        if (!isset($data['role'])) {
            throw DtoValidationException::missingRequiredField('role', 'string');
        }
        if (!isset($data['audience'])) {
            throw DtoValidationException::missingRequiredField('audience', 'string');
        }
        if (!isset($data['created'])) {
            throw DtoValidationException::missingRequiredField('created', 'string');
        }

        if (!is_string($data['id'])) {
            throw DtoValidationException::invalidType('id', 'string', $data['id']);
        }
        if (!is_string($data['description'])) {
            throw DtoValidationException::invalidType('description', 'string', $data['description']);
        }
        if (!is_string($data['role'])) {
            throw DtoValidationException::invalidType('role', 'string', $data['role']);
        }
        if (!is_string($data['audience'])) {
            throw DtoValidationException::invalidType('audience', 'string', $data['audience']);
        }
        if (!is_string($data['created'])) {
            throw DtoValidationException::invalidType('created', 'string', $data['created']);
        }

        return new self(
            id: $data['id'],
            description: $data['description'],
            role: $data['role'],
            audience: $data['audience'],
            created: $data['created'],
            expires: isset($data['expires']) && is_string($data['expires']) ? $data['expires'] : null,
            lastUse: isset($data['lastUse']) && is_string($data['lastUse']) ? $data['lastUse'] : null,
        );
    }
}
