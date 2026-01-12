<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\User;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for deleted user recovery information
 * GET /users/:user/restore
 */
final class RestoreUserInfoDto implements ResponseDtoInterface
{
    public function __construct(
        public bool $success,
        public ?string $code = null,
        public ?string $user = null,
        public ?string $task = null,
        /** @var array<{recovered: int; main: string}>|null */
        public ?array $addresses = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }

        if (!is_bool($data['success'])) {
            throw DtoValidationException::invalidType('success', 'bool', $data['success']);
        }

        return new self(
            success: $data['success'],
            code: $data['code'] ?? null,
            user: $data['user'] ?? null,
            task: $data['task'] ?? null,
            addresses: $data['addresses'] ?? null
        );
    }
}
