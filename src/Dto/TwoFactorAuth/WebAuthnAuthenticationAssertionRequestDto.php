<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\TwoFactorAuth;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for WebAuthN authentication assertion
 */
readonly class WebAuthnAuthenticationAssertionRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string $challenge,
        public string $rawId,
        public string $clientDataJSON,
        public string $authenticatorData,
        public string $signature,
        public ?string $rpId = null,
        public ?bool $token = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'challenge' => $this->challenge,
            'rawId' => $this->rawId,
            'clientDataJSON' => $this->clientDataJSON,
            'authenticatorData' => $this->authenticatorData,
            'signature' => $this->signature,
            'rpId' => $this->rpId,
            'token' => $this->token,
        ], fn($value) => $value !== null);
    }
}
