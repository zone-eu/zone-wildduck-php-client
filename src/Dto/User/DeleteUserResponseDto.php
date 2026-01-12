<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\User;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for user deletion
 */
readonly class DeleteUserResponseDto implements ResponseDtoInterface
{
    /**
     * @param array<mixed> $addresses
     */
    public function __construct(
        public bool $success,
        public ?string $code = null,
        public ?string $user = null,
        /** @var array<{deleted: int}>|null */ public ?array $addresses = null,
        public ?string $deleteAfter = null,
        public ?string $task = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }

        return new self(
            success: $data['success'],
            code: $data['code'] ?? null,
            user: $data['user'] ?? null,
            addresses: $data['addresses'] ?? null,
            deleteAfter: $data['deleteAfter'] ?? null,
            task: $data['task'] ?? null,
        );
    }
}
