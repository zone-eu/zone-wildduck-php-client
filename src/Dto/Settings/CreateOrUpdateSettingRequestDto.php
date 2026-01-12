<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Settings;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Request DTO for creating or updating a setting
 * POST /settings/:key
 */
final class CreateOrUpdateSettingRequestDto implements RequestDtoInterface
{
    public function __construct(
        /** @var mixed Setting value (can be any type) */ public readonly mixed $value,
        public readonly ?string $sess = null,
        public readonly ?string $ip = null,
    ) {
    }

    public function toArray(): array
    {
        $result = ['value' => $this->value];

        if ($this->sess !== null) {
            $result['sess'] = $this->sess;
        }

        if ($this->ip !== null) {
            $result['ip'] = $this->ip;
        }

        return $result;
    }
}
