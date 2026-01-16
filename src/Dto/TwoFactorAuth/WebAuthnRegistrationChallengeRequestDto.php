<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\TwoFactorAuth;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for initiating WebAuthN registration challenge
 */
class WebAuthnRegistrationChallengeRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string $description,
        public string $origin,
        public ?string $authenticatorAttachment = null,
        public ?string $rpId = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'description' => $this->description,
            'origin' => $this->origin,
            'authenticatorAttachment' => $this->authenticatorAttachment,
            'rpId' => $this->rpId,
        ], fn($value) => $value !== null);
    }
}
