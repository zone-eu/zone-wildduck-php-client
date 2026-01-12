<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;

/**
 * Filter query DTO defining what messages to match
 */
final class FilterLimitsResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public readonly ?int $allowed = null,
        public readonly ?int $used = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            allowed: $data['allowed'] ?? null,
            used: $data['used'] ?? null,
        );
    }
}
