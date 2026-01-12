<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for upload message operation
 */
readonly class UploadMessageResponseMessageDto implements ResponseDtoInterface
{
    public function __construct(
        public int $id,
        public string $mailbox,
        public int $size,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'int');
        }
        if (!isset($data['mailbox'])) {
            throw DtoValidationException::missingRequiredField('mailbox', 'string');
        }
        if (!isset($data['size'])) {
            throw DtoValidationException::missingRequiredField('size', 'int');
        }

        return new self(
            id: $data['id'],
            mailbox: $data['mailbox'],
            size: $data['size'],
        );
    }
}
