<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Per-user limit entry DTO: a resolved limit with an optional usage counter
 * and, for time-windowed limits, a reset time to live
 */
readonly class LimitQuotaResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public int $allowed,
        public ?int $used = null,
        public int|false|null $ttl = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['allowed'])) {
            throw DtoValidationException::missingRequiredField('allowed', 'int');
        }

        if (!is_int($data['allowed'])) {
            throw DtoValidationException::invalidType('allowed', 'int', $data['allowed']);
        }

        $used = null;
        if (isset($data['used'])) {
            if (!is_int($data['used'])) {
                throw DtoValidationException::invalidType('used', 'int', $data['used']);
            }
            $used = $data['used'];
        }

        $ttl = null;
        if (isset($data['ttl'])) {
            if ($data['ttl'] !== false && !is_int($data['ttl'])) {
                throw DtoValidationException::invalidType('ttl', 'int|false', $data['ttl']);
            }
            $ttl = $data['ttl'];
        }

        return new self(
            allowed: $data['allowed'],
            used: $used,
            ttl: $ttl,
        );
    }
}
