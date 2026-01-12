<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto;

use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Generic paginated result DTO for list endpoints
 *
 * @template T of ResponseDtoInterface
 */
readonly class PaginatedResultDto
{
    /**
     * @param T[] $results Array of result DTOs
     * @param int $total Total number of results
     * @param string|false|null $nextCursor Cursor for next page (false when no next page)
     * @param string|false|null $previousCursor Cursor for previous page (false when no previous page)
     * @param int $page Current page number
     */
    public function __construct(
        public array $results,
        public int $total,
        public string|false|null $nextCursor = null,
        public string|false|null $previousCursor = null,
        public int $page = 1,
    ) {
    }

    /**
     * Create paginated result from API response
     *
     * @param array $data The API response data
     * @param class-string<T> $dtoClass The DTO class to instantiate for each result
     * @return PaginatedResultDto<T>
     * @throws DtoValidationException
     */
    public static function fromArray(array $data, string $dtoClass): self
    {
        if (!isset($data['results'])) {
            throw DtoValidationException::missingRequiredField('results', 'array');
        }

        if (!is_array($data['results'])) {
            throw DtoValidationException::invalidType('results', 'array', $data['results']);
        }

        /** @var T[] */
        $results = [];
        foreach ($data['results'] as $index => $item) {
            if (!is_array($item)) {
                throw DtoValidationException::invalidType(
                    "results[{$index}]",
                    'array',
                    $item
                );
            }

            $results[] = $dtoClass::fromArray($item);
        }

        $total = $data['total'] ?? count($results);
        if (!is_int($total)) {
            throw DtoValidationException::invalidType('total', 'int', $total);
        }

        $nextCursor = null;
        if (isset($data['nextCursor']) && is_string($data['nextCursor'])) {
            $nextCursor = $data['nextCursor'];
        }

        $previousCursor = null;
        if (isset($data['previousCursor']) && is_string($data['previousCursor'])) {
            $previousCursor = $data['previousCursor'];
        }

        $page = $data['page'] ?? 1;
        if (!is_int($page)) {
            $page = 1;
        }

        /** @var self<T> */
        $instance = new self(
            results: $results,
            total: $total,
            nextCursor: $nextCursor,
            previousCursor: $previousCursor,
            page: $page,
        );

        return $instance;
    }

    /**
     * Check if there is a next page
     */
    public function hasNextPage(): bool
    {
        return $this->nextCursor !== null;
    }

    /**
     * Check if there is a previous page
     */
    public function hasPreviousPage(): bool
    {
        return $this->previousCursor !== null;
    }

    /**
     * Get the count of results in this page
     */
    public function count(): int
    {
        return count($this->results);
    }
}
