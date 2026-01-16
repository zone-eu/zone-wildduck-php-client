<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\DomainAccess;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for creating a blocked domain
 */
class CreateBlockedDomainRequestDto implements RequestDtoInterface
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
