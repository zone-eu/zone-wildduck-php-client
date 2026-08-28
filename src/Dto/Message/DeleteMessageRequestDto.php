<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for deleting a message
 */
class DeleteMessageRequestDto implements RequestDtoInterface
{
    public function __construct(
        public bool $updateThread,
    ) {}

    public function toArray(): array
    {
        return [
            'updateThread' => $this->updateThread,
        ];
    }
}
