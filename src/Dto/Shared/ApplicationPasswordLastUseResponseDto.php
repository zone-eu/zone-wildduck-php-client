<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;

/**
 * Application Password Last Use DTO
 */
readonly class ApplicationPasswordLastUseResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public ?string $time = null,
        public ?string $event = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            time: isset($data['time']) && is_string($data['time']) ? $data['time'] : null,
            event: isset($data['event']) && is_string($data['event']) ? $data['event'] : null,
        );
    }
}
