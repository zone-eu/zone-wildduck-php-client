<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\TwoFactorAuth;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for WebAuthN credential details
 */
final class WebAuthnCredentialDto implements ResponseDtoInterface
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly string $created,
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

        if (!isset($data['description'])) {
            throw DtoValidationException::missingRequiredField('description', 'string');
        }

        if (!is_string($data['description'])) {
            throw DtoValidationException::invalidType('description', 'string', $data['description']);
        }

        if (!isset($data['created'])) {
            throw DtoValidationException::missingRequiredField('created', 'string');
        }

        if (!is_string($data['created'])) {
            throw DtoValidationException::invalidType('created', 'string', $data['created']);
        }

        return new self(
            id: $data['id'],
            description: $data['description'],
            created: $data['created'],
        );
    }
}
