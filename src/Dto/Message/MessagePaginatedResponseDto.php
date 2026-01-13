<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\PaginatedResultDto;
use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Authlog paginated result DTO for list endpoints
 *
 */
readonly class MessagePaginatedResponseDto implements ResponseDtoInterface
{
    /**
     * @param MessageListResponseDto[] $results Array of result DTOs
     */
    public function __construct(
        public array $results,
        public int $total,
        public ?string $specialUse = null,
        public string|false|null $nextCursor = null,
        public string|false|null $previousCursor = null,
        public int $page = 1,
    ) {}

    /**
     * Create paginated result from API response
     *
     * @param array $data The API response data
     * @return MessagePaginatedResponseDto
     * @throws DtoValidationException
     */
    public static function fromArray(array $data): self
    {
        /** @var PaginatedResultDto<MessageListResponseDto> $parentSelf */
        $parentSelf = PaginatedResultDto::fromArray($data, MessageListResponseDto::class);

        $instance = new self(
            results: $parentSelf->results,
            total: $parentSelf->total,
            specialUse: $data['specialUse'] ?? null,
            nextCursor: $parentSelf->nextCursor,
            previousCursor: $parentSelf->previousCursor,
            page: $parentSelf->page
        );

        return $instance;
    }
}
