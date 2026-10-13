<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for getting a system flag's message counters
 */
class FlagCounterRequestDto implements RequestDtoInterface
{
    public function __construct(
        public bool $searchable = true,
    ) {
    }

    public function toArray(): array
    {
        return [
            'searchable' => $this->searchable,
        ];
    }
}
