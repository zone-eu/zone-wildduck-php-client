<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Settings;

use Zone\Wildduck\Dto\ResponseDtoInterface;

/**
 * Response DTO for a single setting
 */
readonly class SettingResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public bool $success,
        public string $key,
        public int|string|null $value,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'] ?? true,
            key: $data['key'] ?? '',
            value: $data['value'] ?? null,
        );
    }
}
