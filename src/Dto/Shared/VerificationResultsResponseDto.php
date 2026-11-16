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
     * @param array{domain: string|null, policy?: string}|null $dmarc
     */
    public function __construct(
        public ?string $dkim = null,
        public ?string $spf = null,
        public ?array $dmarc = null,
        public ?array $tls = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            dkim: isset($data['dkim']) && is_string($data['dkim']) ? $data['dkim'] : null,
            spf: isset($data['spf']) && is_string($data['spf']) ? $data['spf'] : null,
            dmarc: self::parseDmarc($data['dmarc'] ?? null),
            tls: isset($data['tls']) && is_array($data['tls']) ? $data['tls'] : null,
        );
    }

    /**
     * @param mixed $dmarc
     * @return array{domain: string|null, policy?: string}|null
     */
    private static function parseDmarc(mixed $dmarc): ?array
    {
        if (! is_array($dmarc)) {
            return null;
        }

        $result = [
            'domain' => is_string($dmarc['domain'] ?? null) ? $dmarc['domain'] : null,
        ];

        if (is_string($dmarc['policy'] ?? null)) {
            $result['policy'] = $dmarc['policy'];
        }

        return $result;
    }
}
