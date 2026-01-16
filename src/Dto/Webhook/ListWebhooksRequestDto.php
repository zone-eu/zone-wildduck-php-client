<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Webhook;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for listing webhooks
 *
 * @property-read string|null $type Prefix or exact match. Prefix match must end with ".*", eg "channel.*". Use "*" for all types
 * @property-read string|null $user User ID
 * @property-read int|null $limit How many records to return (default: 20)
 * @property-read string|null $next Cursor value for next page, retrieved from nextCursor response value
 * @property-read string|null $previous Cursor value for previous page, retrieved from previousCursor response value
 */
final class ListWebhooksRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?string $type = null,
        public ?string $user = null,
        public ?int $limit = null,
        public ?string $next = null,
        public ?string $previous = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'type' => $this->type,
            'user' => $this->user,
            'limit' => $this->limit,
            'next' => $this->next,
            'previous' => $this->previous,
        ], fn($value) => $value !== null);
    }
}
