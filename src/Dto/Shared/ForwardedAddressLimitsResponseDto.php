<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Forwarded address limits DTO
 */
readonly class ForwardedAddressLimitsResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public ?QuotaResponseDto $forwards = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            forwards: isset($data['forwards']) ? QuotaResponseDto::fromArray($data['forwards']) : null,
        );
    }
}
