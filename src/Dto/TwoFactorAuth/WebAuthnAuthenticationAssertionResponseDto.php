<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\TwoFactorAuth;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for WebAuthn authentication assertion result
 */
final class WebAuthnAuthenticationAssertionResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $token = null,
    ) {}

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }

        if (!is_bool($data['success'])) {
            throw DtoValidationException::invalidType('success', 'bool', $data['success']);
        }

        return new self(
            success: $data['success'],
            token: $data['token'],
            response: WebAuthnAuthenticationAssertionResponseResponseDto::fromArray($data['response'] ?? []),
        );
    }
}
