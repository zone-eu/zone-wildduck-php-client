<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for updating a label (name and/or metadata, preserving the id)
 */
class UpdateLabelRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?string $name = null,
        public ?array $metaData = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            ...($this->name !== null ? ['name' => $this->name] : []),
            ...($this->metaData !== null ? ['metaData' => $this->metaData] : []),
        ];
    }
}
