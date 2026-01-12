<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Settings;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for a single setting entry in the settings list
 */
final readonly class SettingDto implements ResponseDtoInterface
{
    public function __construct(
        public string $key,
        public string|int $value,
        public string $name,
        public string $description,
        public string $type,
        public bool $custom,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['key'])) {
            throw DtoValidationException::missingRequiredField('key', 'string');
        }
        if (!isset($data['value'])) {
            throw DtoValidationException::missingRequiredField('value', 'string');
        }
        if (!isset($data['name'])) {
            throw DtoValidationException::missingRequiredField('name', 'string');
        }
        if (!isset($data['description'])) {
            throw DtoValidationException::missingRequiredField('description', 'string');
        }
        if (!isset($data['type'])) {
            throw DtoValidationException::missingRequiredField('type', 'string');
        }
        if (!isset($data['custom'])) {
            throw DtoValidationException::missingRequiredField('custom', 'boolean');
        }


        if (!is_string($data['key'])) {
            throw DtoValidationException::invalidType('key', 'string', $data['key']);
        }

        return new self(
            key: $data['key'],
            value: $data['value'],
            type: $data['type'],
            description: $data['description'],
            name: $data['name'],
            custom: $data['custom'],
        );
    }
}
