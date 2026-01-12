<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\TwoFactorAuth;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Request DTO for generating TOTP seed
 */
readonly class GenerateTotpRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string $issuer,
        public ?string $label = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'issuer' => $this->issuer,
            'label' => $this->label,
        ], fn($value) => $value !== null);
    }
}
