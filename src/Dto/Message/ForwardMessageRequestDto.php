<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for forwarding a stored message
 */
class ForwardMessageRequestDto implements RequestDtoInterface
{
    /**
     * @param string[]|null $addresses
     */
    public function __construct(
        public ?int $target = null,
        /** @var string[]|null */ public ?array $addresses = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'target' => $this->target,
            'addresses' => $this->addresses,
        ], fn($value) => $value !== null);
    }
}
