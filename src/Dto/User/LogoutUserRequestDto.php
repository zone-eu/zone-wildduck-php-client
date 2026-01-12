<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\User;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for logging out a user
 */
readonly class LogoutUserRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?string $reason = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'reason' => $this->reason,
        ], fn($value) => $value !== null);
    }
}
