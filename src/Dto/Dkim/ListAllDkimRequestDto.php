<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Dkim;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Dto\Traits\PaginatedRequestTrait;

/**
 * DTO for renaming a domain across all addresses
 */
class ListAllDkimRequestDto implements RequestDtoInterface
{
    use PaginatedRequestTrait;

    public function __construct(
        public ?string $query,
        public ?int $limit = null,
        public ?string $next = null,
        public ?string $previous = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->query !== null) {
            $data['query'] = $this->query;
        }

        return array_merge($data, $this->getPaginationArray());
    }
}
