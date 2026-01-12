<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Archive;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for restore task
 */
readonly class RestoreTaskResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public string $task,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['task'])) {
            throw DtoValidationException::missingRequiredField('task', 'string');
        }

        if (!is_string($data['task'])) {
            throw DtoValidationException::invalidType('task', 'string', $data['task']);
        }

        return new self(
            task: $data['task'],
        );
    }
}
