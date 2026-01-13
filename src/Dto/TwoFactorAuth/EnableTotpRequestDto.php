<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\TwoFactorAuth;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Request DTO for enabling TOTP
 */
readonly class EnableTotpRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string $token,
    ) {}

    public function toArray(): array
    {
        return [
            'token' => $this->token,
        ];
    }
}
