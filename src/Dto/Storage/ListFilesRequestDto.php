<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Storage;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for listing stored files
 *
 * @property-read string|null $query Partial match of a filename
 * @property-read int|null $limit How many records to return (default: 20)
 * @property-read string|null $next Cursor value for next page
 * @property-read string|null $previous Cursor value for previous page
 */
final readonly class ListFilesRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?string $query = null,
        public ?int $limit = null,
        public ?string $next = null,
        public ?string $previous = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'query' => $this->query,
            'limit' => $this->limit,
            'next' => $this->next,
            'previous' => $this->previous,
        ], fn($value) => $value !== null);
    }
}
