<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for a system flag's message counters (flagged/unread)
 */
readonly class FlagCounterResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public string $flag,
        public int $total,
        public int $unseen,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['flag'])) {
            throw DtoValidationException::missingRequiredField('flag', 'string');
        }
        if (!isset($data['total'])) {
            throw DtoValidationException::missingRequiredField('total', 'int');
        }
        if (!isset($data['unseen'])) {
            throw DtoValidationException::missingRequiredField('unseen', 'int');
        }
        if (!is_string($data['flag'])) {
            throw DtoValidationException::invalidType('flag', 'string', $data['flag']);
        }
        if (!is_int($data['total'])) {
            throw DtoValidationException::invalidType('total', 'int', $data['total']);
        }
        if (!is_int($data['unseen'])) {
            throw DtoValidationException::invalidType('unseen', 'int', $data['unseen']);
        }

        return new self(
            flag: $data['flag'],
            total: $data['total'],
            unseen: $data['unseen'],
        );
    }
}
