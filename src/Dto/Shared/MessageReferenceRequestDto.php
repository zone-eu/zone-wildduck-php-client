<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Message reference information (reply/forward context)
 */
class MessageReferenceRequestDto implements RequestDtoInterface
{
    /**
     * @param bool|string[]|null $attachments
     */
    public function __construct(
        public string $mailbox,
        public int $id,
        public string $action,
        public bool|array|null $attachments = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'mailbox' => $this->mailbox,
            'id' => $this->id,
            'action' => $this->action,
        ];

        if ($this->attachments !== null) {
            $data['attachments'] = $this->attachments;
        }

        return $data;
    }
}
