<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for a label's message counters
 */
readonly class LabelCounterResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public string $label,
        public int $total,
        public int $unseen,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['label'])) {
            throw DtoValidationException::missingRequiredField('label', 'string');
        }
        if (!isset($data['total'])) {
            throw DtoValidationException::missingRequiredField('total', 'int');
        }
        if (!isset($data['unseen'])) {
            throw DtoValidationException::missingRequiredField('unseen', 'int');
        }
        if (!is_string($data['label'])) {
            throw DtoValidationException::invalidType('label', 'string', $data['label']);
        }
        if (!is_int($data['total'])) {
            throw DtoValidationException::invalidType('total', 'int', $data['total']);
        }
        if (!is_int($data['unseen'])) {
            throw DtoValidationException::invalidType('unseen', 'int', $data['unseen']);
        }

        return new self(
            label: $data['label'],
            total: $data['total'],
            unseen: $data['unseen'],
        );
    }
}
