<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\DomainAccess;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for domain access operations
 */
readonly class DomainAccessResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public string $id,
        public string $domain,
        public string $action,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'string');
        }
        if (!isset($data['domain'])) {
            throw DtoValidationException::missingRequiredField('domain', 'string');
        }
        if (!isset($data['action'])) {
            throw DtoValidationException::missingRequiredField('action', 'string');
        }

        return new self(
            id: $data['id'],
            domain: $data['domain'],
            action: $data['action'],
        );
    }
}
