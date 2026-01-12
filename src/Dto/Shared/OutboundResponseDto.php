<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;

/**
 * Outbound queue information
 */
readonly class OutboundResponseDto implements ResponseDtoInterface
{
    /**
     * @param OutboundQueueEntryResponseDto[] $entries
     */
    public function __construct(
        public string $queueId,
        public array $entries = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        $entries = [];
        if (isset($data['entries']) && is_array($data['entries'])) {
            foreach ($data['entries'] as $entry) {
                if (is_array($entry)) {
                    $entries[] = OutboundQueueEntryResponseDto::fromArray($entry);
                }
            }
        }

        return new self(
            queueId: $data['queueId'] ?? '',
            entries: $entries,
        );
    }
}
