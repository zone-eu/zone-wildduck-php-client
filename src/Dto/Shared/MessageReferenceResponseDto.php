<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;

/**
 * Message reference information (reply/forward context)
 */
readonly class MessageReferenceResponseDto implements ResponseDtoInterface
{
    /**
     * @param 'reply'|'replyAll'|'forward' $action
     * @param bool|string[]|null $attachments
     */
    public function __construct(
        public string $mailbox,
        public int $id,
        public string $action,
        public bool|array|null $attachments = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            mailbox: $data['mailbox'] ?? '',
            id: $data['id'] ?? 0,
            action: $data['action'] ?? '',
            attachments: $data['attachments'] ?? null,
        );
    }
}
