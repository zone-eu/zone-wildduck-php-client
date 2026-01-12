<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Address;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Address DTO representing an email address
 */
readonly class AddressResponseDto implements ResponseDtoInterface
{
    /**
     * @param string[] $tags
     */
    public function __construct(
        public string $id,
        public string $address,
        public bool $main,
        public ?string $name = null,
        public ?string $created = null,
        /** @var string[] */ public array $tags = [],
        /** @var array<string, mixed>|null Custom metadata */ public ?array $metaData = null,
        /** @var array<string, mixed>|null Internal data */ public ?array $internalData = null,
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
        if (!isset($data['main'])) {
            throw DtoValidationException::missingRequiredField('main', 'bool');
        }

        if (!is_string($data['id'])) {
            throw DtoValidationException::invalidType('id', 'string', $data['id']);
        }
        if (!is_string($data['address'])) {
            throw DtoValidationException::invalidType('address', 'string', $data['address']);
        }
        if (!is_bool($data['main'])) {
            throw DtoValidationException::invalidType('main', 'bool', $data['main']);
        }

        return new self(
            id: $data['id'],
            address: $data['address'],
            main: $data['main'],
            name: isset($data['name']) && is_string($data['name']) ? $data['name'] : null,
            created: isset($data['created']) && is_string($data['created']) ? $data['created'] : null,
            tags: isset($data['tags']) && is_array($data['tags']) ? $data['tags'] : [],
            metaData: isset($data['metaData']) && is_array($data['metaData']) ? $data['metaData'] : null,
            internalData: isset($data['internalData']) && is_array($data['internalData']) ? $data['internalData'] : null,
        );
    }
}
