<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\TwoFactorAuth;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for WebAuthN credential deletion
 * DELETE /users/:user/2fa/webauthn/credentials/:credential
 */
final class DeleteWebAuthnCredentialResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public readonly bool $success,
        public readonly bool $deleted,
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

        if (!isset($data['deleted'])) {
            throw DtoValidationException::missingRequiredField('deleted', 'bool');
        }

        if (!is_bool($data['deleted'])) {
            throw DtoValidationException::invalidType('deleted', 'bool', $data['deleted']);
        }

        return new self(
            success: $data['success'],
            deleted: $data['deleted'],
        );
    }
}
