<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Authentication;

use Zone\Wildduck\Dto\PaginatedResultDto;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Authlog paginated result DTO for list endpoints
 *
 */
readonly class AuthlogPaginatedResponseDto
{
    /**
     * @param string $action Array of result DTOs
     * @param AuthLogEventResponseDto[] $results Array of result DTOs
     * @param int $total Total number of results
     * @param string|false|null $nextCursor Cursor for next page (false when no next page)
     * @param string|false|null $previousCursor Cursor for previous page (false when no previous page)
     * @param int $page Current page number
     */
    public function __construct(
        public array $results,
        public int $total,
        public ?string $action = null,
        public string|false|null $nextCursor = null,
        public string|false|null $previousCursor = null,
        public int $page = 1,
    ) {
    }

    /**
     * Create paginated result from API response
     *
     * @param array $data The API response data
     * @return AuthlogPaginatedResponseDto
     * @throws DtoValidationException
     */
    public static function fromArray(array $data): self
    {
        /** @var PaginatedResultDto<AuthLogEventResponseDto> $parentSelf */
        $parentSelf = PaginatedResultDto::fromArray($data, AuthLogEventResponseDto::class);

        $instance = new self(
            results: $parentSelf->results,
            total: $parentSelf->total,
            nextCursor: $parentSelf->nextCursor,
            previousCursor: $parentSelf->previousCursor,
            page: $parentSelf->page,
            action: $data['action'] ?? null
        );

        return $instance;
    }
}
