<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Autoreply;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for autoreply information
 */
readonly class AutoreplyResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public bool $success,
        public ?bool $status = null,
        public ?string $name = null,
        public ?string $subject = null,
        public ?string $text = null,
        public ?string $html = null,
        public string|false|null $start = null,
        public string|false|null $end = null,
        public string|false|null $created = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }

        return new self(
            success: $data['success'],
            status: $data['status'] ?? null,
            name: $data['name'] ?? null,
            subject: $data['subject'] ?? null,
            text: $data['text'] ?? null,
            html: $data['html'] ?? null,
            start: $data['start'] ?? null,
            end: $data['end'] ?? null,
            created: $data['created'] ?? null,
        );
    }
}
