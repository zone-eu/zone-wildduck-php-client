<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Settings;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for getting a single setting value
 * GET /settings/:key
 */
final class GetSettingResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public readonly bool $success,
        public readonly string $key,
        /** @var mixed Setting value (can be any type) */ public readonly mixed $value = null,
        public readonly ?string $error = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }

        if (!is_bool($data['success'])) {
            throw DtoValidationException::invalidType('success', 'bool', $data['success']);
        }

        if (!isset($data['key'])) {
            throw DtoValidationException::missingRequiredField('key', 'string');
        }

        if (!is_string($data['key'])) {
            throw DtoValidationException::invalidType('key', 'string', $data['key']);
        }

        return new self(
            success: $data['success'],
            key: $data['key'],
            value: $data['value'] ?? null,
            error: isset($data['error']) && is_string($data['error']) ? $data['error'] : null,
        );
    }
}
