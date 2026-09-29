<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\ScopedToken;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for exchanging a master API token for a scoped authentication token
 */
readonly class CreateScopedTokenResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public bool $success,
        public string $scope,
        public string $id,
        public string $token,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }
        if (!isset($data['scope'])) {
            throw DtoValidationException::missingRequiredField('scope', 'string');
        }
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'string');
        }
        if (!isset($data['token'])) {
            throw DtoValidationException::missingRequiredField('token', 'string');
        }

        if (!is_bool($data['success'])) {
            throw DtoValidationException::invalidType('success', 'bool', $data['success']);
        }
        if (!is_string($data['scope'])) {
            throw DtoValidationException::invalidType('scope', 'string', $data['scope']);
        }
        if (!is_string($data['id'])) {
            throw DtoValidationException::invalidType('id', 'string', $data['id']);
        }
        if (!is_string($data['token'])) {
            throw DtoValidationException::invalidType('token', 'string', $data['token']);
        }

        return new self(
            success: $data['success'],
            scope: $data['scope'],
            id: $data['id'],
            token: $data['token'],
        );
    }
}
