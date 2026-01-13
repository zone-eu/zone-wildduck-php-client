<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\DomainAlias;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for creating a new domain alias
 */
readonly class CreateDomainAliasRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string $alias,
        public string $domain,
    ) {}

    public function toArray(): array
    {
        return [
            'alias' => $this->alias,
            'domain' => $this->domain,
        ];
    }
}
