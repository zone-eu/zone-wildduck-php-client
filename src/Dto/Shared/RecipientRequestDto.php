<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Recipient information DTO
 */
readonly class RecipientRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?string $name = null,
        public string $address = '',
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'address' => $this->address,
        ];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        return $data;
    }
}
