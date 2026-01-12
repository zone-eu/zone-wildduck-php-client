<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Dkim;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for DKIM key information
 */
readonly class DkimDNSResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public string $name,
        public string $value,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['name'])) {
            throw DtoValidationException::missingRequiredField('name', 'string');
        }
        if (!isset($data['value'])) {
            throw DtoValidationException::missingRequiredField('value', 'string');
        }

        return new self(
            name: $data['name'],
            value: $data['value'],
        );
    }
}
