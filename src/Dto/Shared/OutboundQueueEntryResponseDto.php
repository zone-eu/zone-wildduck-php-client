<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;

/**
 * Outbound queue entry
 */
readonly class OutboundQueueEntryResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public string $seq,
        public string $recipient,
        public string $sendingZone,
        public string $queued,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            seq: $data['seq'] ?? '',
            recipient: $data['recipient'] ?? '',
            sendingZone: $data['sendingZone'] ?? '',
            queued: $data['queued'] ?? '',
        );
    }
}
