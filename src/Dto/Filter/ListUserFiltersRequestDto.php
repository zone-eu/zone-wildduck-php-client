<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Filter;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Dto\Traits\PaginatedRequestTrait;

/**
 * DTO for renaming a domain across all addresses
 */
class ListUserFiltersRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?bool $metaData,
    ) {
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->metaData !== null) {
            $data['metaData'] = $this->metaData;
        }

        return $data;
    }
}
