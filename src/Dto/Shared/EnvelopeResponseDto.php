<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;

/**
 * SMTP envelope information
 */
readonly class EnvelopeResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public ?string $from = null,
        /** @var string[]|null */
        public ?array $rcpt = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            from: $data['from'] ?? null,
            rcpt: $data['rcpt'] ?? null,
        );
    }
}
