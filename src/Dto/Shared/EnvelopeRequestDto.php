<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * SMTP envelope information
 */
class EnvelopeRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?string $from = null,
        /** @var string[]|null */
        public ?array $rcpt = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->from !== null) {
            $data['from'] = $this->from;
        }
        if ($this->rcpt !== null) {
            $data['rcpt'] = $this->rcpt;
        }

        return $data;
    }
}
