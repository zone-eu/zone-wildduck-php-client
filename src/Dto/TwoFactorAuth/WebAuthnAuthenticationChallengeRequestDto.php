<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\TwoFactorAuth;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for WebAuthN authentication challenge
 */
readonly class WebAuthnAuthenticationChallengeRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string $origin,
        public ?string $authenticatorAttachment = null,
        public ?string $rpId = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'origin' => $this->origin,
            'authenticatorAttachment' => $this->authenticatorAttachment,
            'rpId' => $this->rpId,
        ], fn($value) => $value !== null);
    }
}
