<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Mailbox;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for mailbox information
 */
readonly class MailboxResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public string $id,
        public string $name,
        public string $path,
        public string $specialUse = '',
        public int $modifyIndex = 0,
        public bool $subscribed = false,
        public bool $hidden = false,
        public bool $encryptMessages = false,
        public int $total = 0,
        public int $unseen = 0,
        public ?int $size = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'string');
        }
        if (!isset($data['name'])) {
            throw DtoValidationException::missingRequiredField('name', 'string');
        }
        if (!isset($data['path'])) {
            throw DtoValidationException::missingRequiredField('path', 'string');
        }

        return new self(
            id: $data['id'],
            name: $data['name'],
            path: $data['path'],
            specialUse: $data['specialUse'] ?? '',
            modifyIndex: $data['modifyIndex'] ?? 0,
            subscribed: $data['subscribed'] ?? false,
            hidden: $data['hidden'] ?? false,
            encryptMessages: $data['encryptMessages'] ?? false,
            total: $data['total'] ?? 0,
            unseen: $data['unseen'] ?? 0,
            size: $data['size'] ?? null,
        );
    }
}
