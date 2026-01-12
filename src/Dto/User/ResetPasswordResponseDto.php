<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\User;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for password reset operation
 */
final class ResetPasswordResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public readonly bool $success,
        public readonly string $password,
        public readonly ?string $validAfter = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }

        if (!is_bool($data['success'])) {
            throw DtoValidationException::invalidType('success', 'bool', $data['success']);
        }

        if (!isset($data['password'])) {
            throw DtoValidationException::missingRequiredField('password', 'string');
        }

        if (!is_string($data['password'])) {
            throw DtoValidationException::invalidType('password', 'string', $data['password']);
        }

        return new self(
            success: $data['success'],
            password: $data['password'],
            validAfter: isset($data['validAfter']) && is_string($data['validAfter']) ? $data['validAfter'] : null,
        );
    }
}
