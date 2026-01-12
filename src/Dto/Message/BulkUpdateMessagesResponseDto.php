<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for bulk update messages operation
 */
readonly class BulkUpdateMessagesResponseDto implements ResponseDtoInterface
{
    /**
     * @param array<int> $id
     */
    public function __construct(
        public bool $success,
        /** @var int[]|null */ public ?array $id = null,
        public ?string $mailbox = null,
        public ?int $updated = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }

        return new self(
            success: $data['success'],
            id: $data['id'] ?? null,
            mailbox: $data['mailbox'] ?? null,
            updated: $data['updated'] ?? null,
        );
    }
}
