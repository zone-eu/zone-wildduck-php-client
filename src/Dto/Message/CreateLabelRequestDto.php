<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for creating a label
 */
class CreateLabelRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string $name,
        public ?array $metaData = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            ...($this->metaData !== null ? ['metaData' => $this->metaData] : []),
        ];
    }
}
