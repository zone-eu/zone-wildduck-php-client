<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\User;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * DTO for listing registered users with filtering options.
 *
 * @property-read string|null $query Partial match of username or default email address
 * @property-read string|null $forward Partial match of a forward email address or URL
 * @property-read string|null $tags Comma separated list of tags. The User must have at least one to be set
 * @property-read string|null $requiredTags Comma separated list of tags. The User must have all listed tags to be set
 * @property-read bool|null $metaData If true, then includes metaData in the response
 * @property-read bool|null $internalData If true, then includes internalData in the response. Not shown for user-role tokens.
 * @property-read int|null $limit How many records to return (default: 20)
 * @property-read string|null $next Cursor value for next page, retrieved from nextCursor response value
 * @property-read string|null $previous Cursor value for previous page, retrieved from previousCursor response value
 */
final readonly class ListUsersRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?string $query = null,
        public ?string $forward = null,
        public ?string $tags = null,
        public ?string $requiredTags = null,
        public ?bool $metaData = null,
        public ?bool $internalData = null,
        public ?int $limit = null,
        public ?string $next = null,
        public ?string $previous = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'query' => $this->query,
            'forward' => $this->forward,
            'tags' => $this->tags,
            'requiredTags' => $this->requiredTags,
            'metaData' => $this->metaData,
            'internalData' => $this->internalData,
            'limit' => $this->limit,
            'next' => $this->next,
            'previous' => $this->previous,
        ], fn($value) => $value !== null);
    }
}
