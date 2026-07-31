<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\TwoFactorAuth;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for WebAuthn authentication assertion result
 */
final class WebAuthnAuthenticationAssertionResponseResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public readonly bool $authenticated,
        public readonly string $credential,
    ) {}

    public static function fromArray(array $data): self
    {
        if (!isset($data['authenticated'])) {
            throw DtoValidationException::missingRequiredField('authenticated', 'bool');
        }

        if (!is_bool($data['authenticated'])) {
            throw DtoValidationException::invalidType('authenticated', 'bool', $data['authenticated']);
        }

        if (!isset($data['credential'])) {
            throw DtoValidationException::missingRequiredField('credential', 'string');
        }

        if (!is_string($data['credential'])) {
            throw DtoValidationException::invalidType('credential', 'string', $data['credential']);
        }

        return new self(
            authenticated: $data['authenticated'],
            credential: $data['credential'],
        );
    }
}
