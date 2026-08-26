<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for updating an outbound message
 */
class UpdateOutboundMessageRequestDto implements RequestDtoInterface
{
    /**
     * @param string $sendTime
     */
    public function __construct(
        public string $sendTime,
    ) {}

    public function toArray(): array
    {
        return [
            'sendTime' => $this->sendTime,
        ];
    }
}
