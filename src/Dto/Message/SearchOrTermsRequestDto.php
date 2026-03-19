<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\RequestDtoInterface;

class SearchOrTermsRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?string $query = null,
        public ?string $from = null,
        public ?string $to = null,
        public ?string $subject = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'query' => $this->query,
            'from' => $this->from,
            'to' => $this->to,
            'subject' => $this->subject,
        ], fn($value) => $value !== null);
    }
}
