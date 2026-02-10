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
        public string $key,
        public string $action,
        public string $created,
        public int $events,
        public string $expires,
        public string $ip,
        public string $last,
        public ?string $filter = null,
        public ?string $target = null,
        public ?string $protocol = null,
        public string|bool|null $result = null,
        public ?string $sess = null,
        public ?string $requiredScope = null,
        public ?string $aname = null,
        public ?string $asp = null,
        public ?string $source = null
    ) {}

    public static function fromArray(array $data): self
    {
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'string');
        }
        if (!isset($data['key'])) {
            throw DtoValidationException::missingRequiredField('key', 'string');
        }
        if (!isset($data['action'])) {
            throw DtoValidationException::missingRequiredField('action', 'string');
        }
        if (!isset($data['created'])) {
            throw DtoValidationException::missingRequiredField('created', 'string');
        }
        if (!isset($data['events'])) {
            throw DtoValidationException::missingRequiredField('events', 'int');
        }
        if (!isset($data['expires'])) {
            throw DtoValidationException::missingRequiredField('expires', 'string');
        }
        if (!isset($data['ip'])) {
            throw DtoValidationException::missingRequiredField('ip', 'string');
        }
        if (!isset($data['last'])) {
            throw DtoValidationException::missingRequiredField('last', 'string');
        }

        return new self(
            id: $data['id'],
            key: $data['key'],
            action: $data['action'],
            created: $data['created'],
            events: $data['events'],
            expires: $data['expires'],
            ip: $data['ip'],
            last: $data['last'],
            filter: $data['filter'] ?? null,
            target: $data['target'] ?? null,
            protocol: $data['protocol'] ?? null,
            result: $data['result'] ?? null,
            sess: $data['sess'] ?? null,
            requiredScope: $data['requiredScope'] ?? null,
            aname: $data['aname'] ?? null,
            asp: $data['asp'] ?? null,
            source: $data['source'] ?? null,
        );
    }
}
