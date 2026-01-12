<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\TwoFactorAuth;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for listing WebAuthN credentials
 * GET /users/:user/2fa/webauthn/credentials
 */
final class WebAuthnCredentialsResponseDto implements ResponseDtoInterface
{
    /**
     * @param WebAuthnCredentialDto[] $credentials
     */
    public function __construct(
        public readonly bool $success,
        public readonly array $credentials,
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

        if (!isset($data['credentials'])) {
            throw DtoValidationException::missingRequiredField('credentials', 'array');
        }

        if (!is_array($data['credentials'])) {
            throw DtoValidationException::invalidType('credentials', 'array', $data['credentials']);
        }

        $credentials = array_map(
            fn(array $credential) => WebAuthnCredentialDto::fromArray($credential),
            $data['credentials']
        );

        return new self(
            success: $data['success'],
            credentials: $credentials,
        );
    }
}
