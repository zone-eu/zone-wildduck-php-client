<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Key information DTO for encryption keys
 */
readonly class KeyInfoResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public string $name,
        public string $address,
        public string $fingerprint,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['name'])) {
            throw DtoValidationException::missingRequiredField('name', 'string');
        }
        if (!isset($data['address'])) {
            throw DtoValidationException::missingRequiredField('address', 'string');
        }
        if (!isset($data['fingerprint'])) {
            throw DtoValidationException::missingRequiredField('fingerprint', 'string');
        }

        if (!is_string($data['name'])) {
            throw DtoValidationException::invalidType('name', 'string', $data['name']);
        }
        if (!is_string($data['address'])) {
            throw DtoValidationException::invalidType('address', 'string', $data['address']);
        }
        if (!is_string($data['fingerprint'])) {
            throw DtoValidationException::invalidType('fingerprint', 'string', $data['fingerprint']);
        }

        return new self(
            name: $data['name'],
            address: $data['address'],
            fingerprint: $data['fingerprint'],
        );
    }
}
