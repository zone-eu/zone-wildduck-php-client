<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for the list of a user's labels
 */
readonly class UserLabelsResponseDto implements ResponseDtoInterface
{
    /**
     * @param LabelResponseDto[] $labels Labels of the user
     */
    public function __construct(
        public bool $success,
        public array $labels,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }
        if (!isset($data['labels'])) {
            throw DtoValidationException::missingRequiredField('labels', 'LabelResponseDto[]');
        }

        return new self(
            success: $data['success'],
            labels: array_map(fn($label) => LabelResponseDto::fromArray($label), $data['labels']),
        );
    }
}
