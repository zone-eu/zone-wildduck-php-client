<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Health;

use Zone\Wildduck\Dto\ResponseDtoInterface;

/**
 * Response DTO for successful health check
 */
readonly class HealthCheckResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public bool $success,
        /**
         * The running WildDuck API version, or null when the server predates
         * the version field in its /health response (WildDuck < 1.46.3).
         */
        public ?string $version = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'] ?? true,
            version: $data['version'] ?? null,
        );
    }
}
