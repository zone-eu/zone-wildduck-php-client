<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Address;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * DTO for resolving an address
 */
class ResolveAddressRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?bool $allowWildcard = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->allowWildcard !== null) {
            $data['allowWildcard'] = $this->allowWildcard;
        }

        return $data;
    }
}
