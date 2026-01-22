<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Filter query DTO defining what messages to match
 */
final class FilterQueryRequestDto implements RequestDtoInterface
{
    public function __construct(
        public readonly ?string $from = null,
        public readonly ?string $to = null,
        public readonly ?string $subject = null,
        public readonly ?string $listId = null,
        public readonly ?string $text = null,
        public readonly ?bool $ha = null,
        public readonly ?int $size = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'from' => $this->from ?? '',
            'to' => $this->to ?? '',
            'subject' => $this->subject ?? '',
            'listId' => $this->listId ?? '',
            'text' => $this->text ?? '',
            'ha' => $this->ha ?? false,
            'size' => $this->size ?? 0,
        ];
    }
}
