<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\DomainAlias;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for domain alias information
 */
readonly class DomainAliasResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public string $id,
        public string $alias,
        public string $domain,
        public ?string $created = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'string');
        }
        if (!isset($data['alias'])) {
            throw DtoValidationException::missingRequiredField('alias', 'string');
        }
        if (!isset($data['domain'])) {
            throw DtoValidationException::missingRequiredField('domain', 'string');
        }

        return new self(
            id: $data['id'],
            alias: $data['alias'],
            domain: $data['domain'],
            created: $data['created'] ?? null,
        );
    }
}
