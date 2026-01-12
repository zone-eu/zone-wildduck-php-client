<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\ApplicationPassword;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Dto\Shared\ApplicationPasswordLastUseResponseDto;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Create application Password DTO
 */
readonly class CreateApplicationPasswordResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public string $id,
        public string $password,
        public ?string $mobileconfig = null,
        public ?string $name = null,
        public ?string $address = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'string');
        }
        if (!isset($data['password'])) {
            throw DtoValidationException::missingRequiredField('password', 'string');
        }

        if (!is_string($data['id'])) {
            throw DtoValidationException::invalidType('id', 'string', $data['id']);
        }
        if (!is_string($data['password'])) {
            throw DtoValidationException::invalidType('password', 'string', $data['password']);
        }

        return new self(
            id: $data['id'],
            password: $data['password'],
            mobileconfig: isset($data['mobileconfig']) && is_string($data['mobileconfig']) ? $data['mobileconfig'] : null,
            name: isset($data['name']) && is_string($data['name']) ? $data['name'] : null,
            address: isset($data['address']) && is_string($data['address']) ? $data['address'] : null,
        );
    }
}
