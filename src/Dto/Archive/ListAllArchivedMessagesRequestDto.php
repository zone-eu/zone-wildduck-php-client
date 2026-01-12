<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Archive;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Dto\Traits\PaginatedRequestTrait;

/**
 * DTO for renaming a domain across all addresses
 */
readonly class ListAllArchivedMessagesRequestDto implements RequestDtoInterface
{
    use PaginatedRequestTrait;

    /**
     * @param 'asc'|'desc' $order
     */
    public function __construct(
        public ?int $limit = null,
        public ?string $next = null,
        public ?string $previous = null,
        public ?string $order = null,
        public ?string $includeHeaders = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [];

        return array_merge($data, $this->getPaginationArray());
    }
}
