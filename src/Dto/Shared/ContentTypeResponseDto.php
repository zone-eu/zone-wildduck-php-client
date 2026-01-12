<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Content-Type header information
 */
readonly class ContentTypeResponseDto implements ResponseDtoInterface
{
    /**
     * @param array<string, mixed>|null $params
     */
    public function __construct(
        public string $value,
        public ?array $params = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['value'])) {
            throw DtoValidationException::missingRequiredField('value', 'string');
        }

        if (!is_string($data['value'])) {
            throw DtoValidationException::invalidType('value', 'string', $data['value']);
        }

        return new self(
            value: $data['value'],
            params: isset($data['params']) && is_array($data['params']) ? $data['params'] : null,
        );
    }
}
