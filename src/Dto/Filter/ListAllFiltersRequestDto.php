<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Filter;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Dto\Traits\PaginatedRequestTrait;

/**
 * DTO for renaming a domain across all addresses
 */
readonly class ListAllFiltersRequestDto implements RequestDtoInterface
{
    use PaginatedRequestTrait;

    public function __construct(
        public ?string $forward,
        public ?bool $metaData,
        public ?int $limit = null,
        public ?string $next = null,
        public ?string $previous = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->forward !== null) {
            $data['forward'] = $this->forward;
        }
        if ($this->metaData !== null) {
            $data['metaData'] = $this->metaData;
        }

        return array_merge($data, $this->getPaginationArray());
    }
}
