<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Address;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Address Register entry DTO
 */
readonly class AddressRegisterResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public string $id,
        public string $address,
        public ?string $name = null,
        public ?bool $disabled = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'string');
        }
        if (!isset($data['address'])) {
            throw DtoValidationException::missingRequiredField('address', 'string');
        }

        if (!is_string($data['id'])) {
            throw DtoValidationException::invalidType('id', 'string', $data['id']);
        }
        if (!is_string($data['address'])) {
            throw DtoValidationException::invalidType('address', 'string', $data['address']);
        }

        return new self(
            id: $data['id'],
            address: $data['address'],
            name: isset($data['name']) && is_string($data['name']) ? $data['name'] : null,
            disabled: isset($data['disabled']) && is_bool($data['disabled']) ? $data['disabled'] : null,
        );
    }
}
