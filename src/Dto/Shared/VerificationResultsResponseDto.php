<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;

/**
 * Message verification results (DKIM, SPF, DMARC)
 */
readonly class VerificationResultsResponseDto implements ResponseDtoInterface
{
    /**
     * @param array<string, mixed>|null $tls
     */
    public function __construct(
        public ?string $dkim = null,
        public ?string $spf = null,
        public ?string $dmarc = null,
        public ?array $tls = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            dkim: isset($data['dkim']) && is_string($data['dkim']) ? $data['dkim'] : null,
            spf: isset($data['spf']) && is_string($data['spf']) ? $data['spf'] : null,
            dmarc: isset($data['dmarc']) && is_string($data['dmarc']) ? $data['dmarc'] : null,
            tls: isset($data['tls']) && is_array($data['tls']) ? $data['tls'] : null,
        );
    }
}
