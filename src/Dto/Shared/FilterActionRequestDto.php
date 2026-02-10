<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Filter action DTO defining what to do with matched messages
 */
final class FilterActionRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly ?bool $seen = null,
        public readonly ?bool $flag = null,
        public readonly ?bool $delete = null,
        public readonly ?bool $spam = null,
        public readonly ?string $mailbox = null,
        /** @var string[]|null */
        public readonly ?array $targets = null,
    ) {}

    public function toArray(): array
    {
        return array_filter(
            [
                'seen' => $this->seen,
                'flag' => $this->flag,
                'delete' => $this->delete,
                'spam' => $this->spam,
                'mailbox' => $this->mailbox,
                'targets' => $this->targets,
            ],
            // Spam is a tristate where null is also a valid option
            fn($value, $key) => $value !== null || $key === 'spam',
            ARRAY_FILTER_USE_BOTH
        );
    }
}
