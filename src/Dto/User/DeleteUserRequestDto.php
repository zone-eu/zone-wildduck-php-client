<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\User;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for user deletion
 */
readonly class DeleteUserRequestDto implements RequestDtoInterface
{
    public function __construct(
        public bool|string $deleteAfter = false,
    ) {}

    public function toArray(): array
    {
        return [
            'deleteAfter' => $this->deleteAfter,
        ];
    }
}
