<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\User;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for quota recalculation of all users
 * POST /quota/reset
 */
readonly class QuotaRecalculationTaskResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public bool $success,
        public string $task,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }
        if (!isset($data['task'])) {
            throw DtoValidationException::missingRequiredField('task', 'string');
        }

        return new self(
            success: $data['success'],
            task: $data['task'],
        );
    }
}
