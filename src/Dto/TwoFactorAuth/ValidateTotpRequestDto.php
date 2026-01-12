<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\TwoFactorAuth;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Request DTO for validating TOTP token
 * POST /users/:user/2fa/totp/check
 */
readonly class ValidateTotpRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string $token,
    ) {
    }

    public function toArray(): array
    {
        return [
            'token' => $this->token,
        ];
    }
}
