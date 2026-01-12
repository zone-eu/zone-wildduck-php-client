<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Address;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * All address DTO
 */
readonly class AllAddressResponseDto implements ResponseDtoInterface
{
    /**
     * @param string[] $tags
     */
    public function __construct(
        public string $id,
        public string $address,
        public string $user = '',
        public bool $forwarded = false,
        public bool $forwardedDisabled = false,
        public ?string $name = null,
        /** @var string[] */ public array $tags = [],
        /** @var string[] */ public array $targets = [],
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

        if (!is_string($data['id'])) {
            throw DtoValidationException::invalidType('id', 'string', $data['id']);
        }
        if (!is_string($data['address'])) {
            throw DtoValidationException::invalidType('address', 'string', $data['address']);
        }
        if (isset($data['forwardedDisabled']) && !is_bool($data['forwardedDisabled'])) {
            throw DtoValidationException::invalidType('forwardedDisabled', 'bool', $data['forwardedDisabled']);
        }

        return new self(
            id: $data['id'],
            address: $data['address'],
            user: isset($data['user']) && is_string($data['user']) ? $data['user'] : '',
            forwarded: isset($data['forwarded']) && is_bool($data['forwarded']) ? $data['forwarded'] : false,
            forwardedDisabled: isset($data['forwardedDisabled']) && is_bool($data['forwardedDisabled']) ? $data['forwardedDisabled'] : false,
            name: isset($data['name']) && is_string($data['name']) ? $data['name'] : null,
            tags: isset($data['tags']) && is_array($data['tags']) ? $data['tags'] : [],
            targets: isset($data['targets']) && is_array($data['targets']) ? $data['targets'] : [],
            metaData: isset($data['metaData']) && is_array($data['metaData']) ? $data['metaData'] : null,
            internalData: isset($data['internalData']) && is_array($data['internalData']) ? $data['internalData'] : null,
        );
    }
}
