<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;

/**
 * Recipient information DTO
 */
readonly class RecipientResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public string $address,
        public ?string $name = null,
    ) {}

    /** @param array{address: string|null, name: string|null, group?: string } $data */
    public static function fromArray(array $data): self
    {
        return new self(
            address: $data['address'] ?? '',
            name: $data['name'] ?? null,
        );
    }
}
