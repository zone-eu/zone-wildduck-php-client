<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * SMTP envelope information
 */
readonly class EnvelopeResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public string $from,
        /** @var string[]|null */
        public ?array $rcpt = null,
    ) {}

    public static function fromArray(array $data): self
    {
        if (!isset($data['from'])) {
            throw DtoValidationException::missingRequiredField('from', 'string');
        }
        return new self(
            from: $data['from'],
            rcpt: $data['rcpt'] ?? null,
        );
    }
}
