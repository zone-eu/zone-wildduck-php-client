<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Archive;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * DTO for restoring archived messages by date range
 */
class RestoreArchivedMessagesRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string $start,
        public string $end,
    ) {
    }

    public function toArray(): array
    {
        return [
            'start' => $this->start,
            'end' => $this->end,
        ];
    }
}
