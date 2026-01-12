<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * BIMI (Brand Indicators for Message Identification) information
 */
readonly class ListBimiResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public string $domain,
        public ?string $selector = null,
    ) {
    }

    public static function fromArray(array $data): self
    {

        if (!isset($data['domain'])) {
            throw DtoValidationException::missingRequiredField('domain', 'string');
        }

        if (!is_string($data['domain'])) {
            throw DtoValidationException::invalidType('domain', 'string', $data['domain']);
        }

        return new self(
            domain: $data['domain'],
            selector: $data['selector'] ?? null,
        );
    }

    public function toArray(): array
    {
        $data = [
            'domain' => $this->domain,
        ];

        if ($this->selector !== null) {
            $data['selector'] = $this->selector;
        }

        return $data;
    }
}
