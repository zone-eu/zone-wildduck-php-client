<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\TwoFactorAuth;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for validating TOTP token
 * POST /users/:user/2fa/totp/check
 */
class ValidateTotpRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string $token,
        public string $totpNonce
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'token' => $this->token,
            'totpNonce' => $this->totpNonce,
        ]);
    }
}
