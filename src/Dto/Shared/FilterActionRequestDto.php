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
        return [
            'seen' => $this->seen ?? false,
            'flag' => $this->flag ?? false,
            'delete' => $this->delete ?? false,
            'spam' => $this->spam ?? false,
            'mailbox' => $this->mailbox ?? '',
            'targets' => $this->targets ?? [],
        ];
    }
}
