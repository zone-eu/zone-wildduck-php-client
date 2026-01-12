<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Address;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Dto\Traits\MetaDataSupportTrait;
use Zone\Wildduck\Dto\Traits\TaggableTrait;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * DTO for updating an existing address
 */
readonly class UpdateAddressRequestDto implements RequestDtoInterface
{
    use MetaDataSupportTrait;
    use TaggableTrait;

    /**
     * @param string[] $tags
     */
    public function __construct(
        public ?string $name = null,
        public ?string $address = null,
        public ?bool $main = null,
        /** @var string[] */ public array $tags = [],
        /** @var array<string, mixed>|null Custom metadata */ public ?array $metaData = null,
        /** @var array<string, mixed>|null Internal data */ public ?array $internalData = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }
        if ($this->address !== null) {
            $data['address'] = $this->address;
        }
        if ($this->main !== null) {
            $data['main'] = $this->main;
        }

        return array_merge($data, $this->getMetaDataArray(), $this->getTagsArray());
    }
}
