<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\TwoFactorAuth;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for generated TOTP seed
 */
readonly class TotpSeedResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public bool $success,
        public string $seed,
        public string $qrcode,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }
        if (!isset($data['seed'])) {
            throw DtoValidationException::missingRequiredField('seed', 'string');
        }
        if (!isset($data['qrcode'])) {
            throw DtoValidationException::missingRequiredField('qrcode', 'string');
        }

        return new self(
            success: $data['success'],
            seed: $data['seed'],
            qrcode: $data['qrcode'],
        );
    }
}
