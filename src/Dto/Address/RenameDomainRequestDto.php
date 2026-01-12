<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Address;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * DTO for renaming a domain across all addresses
 */
readonly class RenameDomainRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string $oldDomain,
        public string $newDomain,
    ) {
    }

    public function toArray(): array
    {
        return [
            'oldDomain' => $this->oldDomain,
            'newDomain' => $this->newDomain,
        ];
    }
}
