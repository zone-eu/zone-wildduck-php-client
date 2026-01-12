<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\User;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for canceling user deletion
 */
final class CancelUserDeletionResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public readonly bool $success,
        public readonly string $code,
        public readonly ?string $user = null,
        public readonly ?string $task = null,
        public readonly ?array $addresses = null,
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

        if (!isset($data['code'])) {
            throw DtoValidationException::missingRequiredField('code', 'string');
        }

        if (!is_string($data['code'])) {
            throw DtoValidationException::invalidType('code', 'string', $data['code']);
        }

        return new self(
            success: $data['success'],
            code: $data['code'],
            user: isset($data['user']) && is_string($data['user']) ? $data['user'] : null,
            task: isset($data['task']) && is_string($data['task']) ? $data['task'] : null,
            addresses: isset($data['addresses']) && is_array($data['addresses']) ? $data['addresses'] : null,
        );
    }
}
