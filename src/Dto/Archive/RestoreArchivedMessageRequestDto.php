<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Archive;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * DTO for restoring a single archived message
 */
class RestoreArchivedMessageRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?string $mailbox = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->mailbox !== null) {
            $data['mailbox'] = $this->mailbox;
        }

        return $data;
    }
}
