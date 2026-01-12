<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Address;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * DTO for updating address register entry
 */
readonly class UpdateAddressRegisterRequestDto implements RequestDtoInterface
{
    public function __construct(
        public bool $disabled,
        public ?string $name = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'disabled' => $this->disabled,
        ];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        return $data;
    }
}
