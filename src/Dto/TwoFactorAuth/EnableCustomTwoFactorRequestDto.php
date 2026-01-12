<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\TwoFactorAuth;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Request DTO for enabling custom 2FA
 * PUT /users/:user/2fa/custom
 * Disables account password for IMAP/POP3/SMTP
 */
readonly class EnableCustomTwoFactorRequestDto implements RequestDtoInterface
{
    public function __construct()
    {
    }

    public function toArray(): array
    {
        return [];
    }
}
