<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Archive;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for restore operations
 */
readonly class RestoreResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public string $mailbox,
        public int $id,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['mailbox'])) {
            throw DtoValidationException::missingRequiredField('mailbox', 'string');
        }
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'int');
        }

        if (!is_string($data['mailbox'])) {
            throw DtoValidationException::invalidType('mailbox', 'string', $data['mailbox']);
        }
        if (!is_int($data['id'])) {
            throw DtoValidationException::invalidType('id', 'int', $data['id']);
        }

        return new self(
            mailbox: $data['mailbox'],
            id: $data['id'],
        );
    }
}
