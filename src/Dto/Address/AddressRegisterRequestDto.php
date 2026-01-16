<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Address;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Address Register entries request DTO
 */
class AddressRegisterRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string $query
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'query' => $this->query,
        ];

        return $data;
    }
}
