<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Address;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Dto\Traits\MetaDataSupportTrait;

/**
 * DTO for renaming a domain across all addresses
 */
class ListUserRegisteredAddressesRequestDto implements RequestDtoInterface
{
    use MetaDataSupportTrait;

    public function __construct(
        public ?bool $metaData = null,
        public ?bool $internalData = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [];

        return array_merge($data, $this->getMetaDataArray());
    }
}
