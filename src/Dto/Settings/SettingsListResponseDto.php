<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Settings;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for listing settings
 * GET /settings
 */
final class SettingsListResponseDto implements ResponseDtoInterface
{
    /**
     * @param SettingDto[] $settings
     */
    public function __construct(
        public readonly bool $success,
        public readonly array $settings,
        public readonly ?string $filter = null,
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

        if (!isset($data['settings'])) {
            throw DtoValidationException::missingRequiredField('settings', 'array');
        }

        if (!is_array($data['settings'])) {
            throw DtoValidationException::invalidType('settings', 'array', $data['settings']);
        }

        $settings = array_map(
            fn(array $setting) => SettingDto::fromArray($setting),
            $data['settings']
        );

        return new self(
            success: $data['success'],
            settings: $settings,
            filter: isset($data['filter']) && is_string($data['filter']) ? $data['filter'] : null,
        );
    }
}
