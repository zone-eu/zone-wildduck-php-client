<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\TwoFactorAuth;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for WebAuthN registration attestation
 */
class WebAuthnRegistrationAttestationRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string $challenge,
        public string $rawId,
        public string $clientDataJSON,
        public string $attestationObject,
        public ?string $rpId = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'challenge' => $this->challenge,
            'rawId' => $this->rawId,
            'clientDataJSON' => $this->clientDataJSON,
            'attestationObject' => $this->attestationObject,
            'rpId' => $this->rpId,
        ], fn($value) => $value !== null);
    }
}
