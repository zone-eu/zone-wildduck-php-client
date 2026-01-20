<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;

/**
 * Mailing list information
 */
readonly class MailingListResponseDto implements ResponseDtoInterface
{
    /**
     * @param array{ address: string, name: string } $id Mailing list identifier
     * @param array<int, array{ address: string, name: string }> $unsubscribe Mailing list unsubscribe addresses
     */
    public function __construct(
        public array $id,
        public array $unsubscribe,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? [],
            unsubscribe: $data['unsubscribe'] ?? [],
        );
    }
}
