<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Dkim;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Request DTO for creating or updating DKIM key
 */
readonly class CreateOrUpdateDkimRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string $domain,
        public string $selector,
        public ?string $privateKey = null,
        public ?string $description = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'domain' => $this->domain,
            'selector' => $this->selector,
            'privateKey' => $this->privateKey,
            'description' => $this->description,
        ], fn($value) => $value !== null);
    }
}
