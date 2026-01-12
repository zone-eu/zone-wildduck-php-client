<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Authentication;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for authentication event
 */
readonly class AuthLogEventResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public string $id,
        public string $action,
        public string|bool $result,
        public string $created,
        public string $last,
        public int $events,
        public string $expires,
        public ?string $protocol = null,
        public ?string $requiredScope = null,
        public ?string $source = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'string');
        }
        if (!isset($data['action'])) {
            throw DtoValidationException::missingRequiredField('action', 'string');
        }
        if (!isset($data['result'])) {
            throw DtoValidationException::missingRequiredField('result', 'string|bool');
        }
        if (!isset($data['created'])) {
            throw DtoValidationException::missingRequiredField('created', 'string');
        }
        if (!isset($data['last'])) {
            throw DtoValidationException::missingRequiredField('last', 'string');
        }
        if (!isset($data['events'])) {
            throw DtoValidationException::missingRequiredField('events', 'int');
        }
        if (!isset($data['expires'])) {
            throw DtoValidationException::missingRequiredField('expires', 'string');
        }

        return new self(
            id: $data['id'],
            action: $data['action'],
            result: $data['result'],
            created: $data['created'],
            last: $data['last'],
            events: $data['events'],
            expires: $data['expires'],
            protocol: $data['protocol'] ?? null,
            requiredScope: $data['requiredScope'] ?? null,
            source: $data['source'] ?? null,
        );
    }
}
