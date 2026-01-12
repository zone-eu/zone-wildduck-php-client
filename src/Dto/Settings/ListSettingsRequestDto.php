<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Settings;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for listing settings
 */
readonly class ListSettingsRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?string $filter = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'filter' => $this->filter,
        ], fn($value) => $value !== null);
    }
}
