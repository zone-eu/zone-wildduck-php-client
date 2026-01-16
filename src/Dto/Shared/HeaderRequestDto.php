<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Email header information
 */
class HeaderRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string $key,
        public string $value,
    ) {
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'value' => $this->value,
        ];
    }
}
