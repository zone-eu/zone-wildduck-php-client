<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Mailbox;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for deleting a mailbox
 */
class DeleteMailboxRequestDto implements RequestDtoInterface
{
    public function __construct()
    {
    }

    public function toArray(): array
    {
        return [];
    }
}
