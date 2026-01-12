<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\User;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for quota recalculation
 */
readonly class QuotaRecalculationResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public bool $success,
        public int $storageUsed,
        public int $previousStorageUsed,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }
        if (!isset($data['storageUsed'])) {
            throw DtoValidationException::missingRequiredField('storageUsed', 'int');
        }
        if (!isset($data['previousStorageUsed'])) {
            throw DtoValidationException::missingRequiredField('previousStorageUsed', 'int');
        }

        return new self(
            success: $data['success'],
            storageUsed: $data['storageUsed'],
            previousStorageUsed: $data['previousStorageUsed'],
        );
    }
}
