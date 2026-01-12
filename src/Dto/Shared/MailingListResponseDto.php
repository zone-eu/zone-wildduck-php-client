<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;

/**
 * Mailing list information
 */
readonly class MailingListResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public string $id,
        public ?string $unsubscribe = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            unsubscribe: isset($data['unsubscribe']) && is_string($data['unsubscribe']) ? $data['unsubscribe'] : null,
        );
    }
}
