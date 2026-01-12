<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\DomainAccess;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for creating an allowed domain
 */
readonly class CreateAllowedDomainRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string $domain,
    ) {
    }

    public function toArray(): array
    {
        return [
            'domain' => $this->domain,
        ];
    }
}
