<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Submission;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for submit message operation
 */
readonly class SubmitMessageResponseDto implements ResponseDtoInterface
{
    /**
     * @param array<mixed> $message
     */
    public function __construct(
        public bool $success,
        public array $message,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }
        if (!isset($data['message'])) {
            throw DtoValidationException::missingRequiredField('message', 'object');
        }

        return new self(
            success: $data['success'],
            message: $data['message'],
        );
    }
}
