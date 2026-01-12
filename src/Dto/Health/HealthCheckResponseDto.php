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
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'] ?? true,
        );
    }
}
