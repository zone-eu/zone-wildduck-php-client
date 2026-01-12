<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Quota information DTO used across multiple resources
 */
readonly class QuotaResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public int $allowed,
        public int $used,
        public int|false|null $ttl = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['allowed'])) {
            throw DtoValidationException::missingRequiredField('allowed', 'int');
        }
        if (!isset($data['used'])) {
            throw DtoValidationException::missingRequiredField('used', 'int');
        }

        if (!is_int($data['allowed'])) {
            throw DtoValidationException::invalidType('allowed', 'int', $data['allowed']);
        }
        if (!is_int($data['used'])) {
            throw DtoValidationException::invalidType('used', 'int', $data['used']);
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
            used: $data['used'],
            ttl: $ttl,
        );
    }
}
