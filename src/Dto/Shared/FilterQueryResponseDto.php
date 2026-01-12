<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;

/**
 * Filter query DTO defining what messages to match
 */
final class FilterQueryResponseDto implements ResponseDtoInterface
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

    public static function fromArray(array $data): self
    {
        return new self(
            from: $data['from'] ?? null,
            to: $data['to'] ?? null,
            subject: $data['subject'] ?? null,
            listId: $data['listId'] ?? null,
            text: $data['text'] ?? null,
            ha: $data['ha'] ?? null,
            size: $data['size'] ?? null,
        );
    }
}
