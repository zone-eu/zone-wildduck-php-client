<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Address;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Dto\Traits\MetaDataSupportTrait;
use Zone\Wildduck\Dto\Traits\TaggableTrait;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * DTO for creating a new address
 */
class CreateAddressRequestDto implements RequestDtoInterface
{
    use MetaDataSupportTrait;
    use TaggableTrait;

    /**
     * @param string[] $tags
     */
    public function __construct(
        public string $address,
        public ?string $name = null,
        public ?bool $main = null,
        public ?bool $allowWildcard = null,
        /** @var string[] */ public array $tags = [],
        /** @var array<string, mixed>|null Custom metadata */ public ?array $metaData = null,
        /** @var array<string, mixed>|null Internal data */ public ?array $internalData = null,
    ) {
        if (empty($address)) {
            throw DtoValidationException::missingRequiredField('address', 'string');
        }
    }

    public function toArray(): array
    {
        $data = [
            'address' => $this->address,
        ];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }
        if ($this->main !== null) {
            $data['main'] = $this->main;
        }
        if ($this->allowWildcard !== null) {
            $data['allowWildcard'] = $this->allowWildcard;
        }

        return array_merge($data, $this->getMetaDataArray(), $this->getTagsArray());
    }
}
