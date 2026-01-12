<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Address;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Dto\Shared\ForwardedAddressLimitsResponseDto;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Resolved Address DTO - can be either user address or forwarded address
 */
readonly class ResolvedAddressResponseDto implements ResponseDtoInterface
{
    /**
     * @param string[] $targets
     * @param string[] $tags
     */
    public function __construct(
        public bool $success,
        public string $id,
        public string $address,
        public ?string $name = null,
        public ?string $created = null,
        public ?array $targets = null,
        public ?ForwardedAddressLimitsResponseDto $limits = null,
        public ?AutoreplyResponseDto $autoreply = null,
        public array $tags = [],
        /** @var array<string, mixed>|null Custom metadata */ public ?array $metaData = null,
        /** @var array<string, mixed>|null Internal data */ public ?array $internalData = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'string');
        }
        if (!isset($data['address'])) {
            throw DtoValidationException::missingRequiredField('address', 'string');
        }

        if (!is_bool($data['success'])) {
            throw DtoValidationException::invalidType('success', 'bool', $data['success']);
        }
        if (!is_string($data['id'])) {
            throw DtoValidationException::invalidType('id', 'string', $data['id']);
        }
        if (!is_string($data['address'])) {
            throw DtoValidationException::invalidType('address', 'string', $data['address']);
        }

        $name = $data['name'] ?? null;
        if ($name !== null && !is_string($name)) {
            throw DtoValidationException::invalidType('name', 'string', $name);
        }

        $limits = null;
        if (isset($data['limits']) && is_array($data['limits'])) {
            $limits = ForwardedAddressLimitsResponseDto::fromArray($data['limits']);
        }

        $autoreply = null;
        if (isset($data['autoreply']) && is_array($data['autoreply'])) {
            $autoreply = AutoreplyResponseDto::fromArray($data['autoreply']);
        }

        return new self(
            success: $data['success'],
            id: $data['id'],
            address: $data['address'],
            name: $name,
            created: isset($data['created']) && is_string($data['created']) ? $data['created'] : null,
            targets: isset($data['targets']) && is_array($data['targets']) ? $data['targets'] : null,
            limits: $limits,
            autoreply: $autoreply,
            tags: isset($data['tags']) && is_array($data['tags']) ? $data['tags'] : [],
            metaData: isset($data['metaData']) && is_array($data['metaData']) ? $data['metaData'] : null,
            internalData: isset($data['internalData']) && is_array($data['internalData']) ? $data['internalData'] : null,
        );
    }
}
