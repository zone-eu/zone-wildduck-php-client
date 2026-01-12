<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\User;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Request DTO for resetting user password
 * POST /users/:user/password/reset
 * Generates a new temporary password and removes all 2FA settings
 */
final class ResetPasswordRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string|bool|null $validAfter = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'validAfter' => $this->validAfter,
        ], fn($value) => $value !== null);
    }
}
