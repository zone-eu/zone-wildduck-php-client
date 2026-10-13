<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for listing a user's labels
 */
class ListLabelsRequestDto implements RequestDtoInterface
{
    public function __construct(
        public bool $counters = false,
        public bool $searchable = true,
    ) {
    }

    public function toArray(): array
    {
        return [
            'counters' => $this->counters,
            'searchable' => $this->searchable,
        ];
    }
}
