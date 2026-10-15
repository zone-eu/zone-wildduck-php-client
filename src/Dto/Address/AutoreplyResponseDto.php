<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Address;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Dto\Shared\ForwardedAddressLimitsResponseDto;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Autoreply configuration DTO
 */
readonly class AutoreplyResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public ?bool $status = null,
        public string|bool|null $start = null,
        public string|bool|null $end = null,
        public ?string $name = null,
        public ?string $subject = null,
        public ?string $text = null,
        public ?string $html = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            status: isset($data['status']) && is_bool($data['status']) ? $data['status'] : null,
            start: $data['start'] ?? null,
            end: $data['end'] ?? null,
            name: isset($data['name']) && is_string($data['name']) ? $data['name'] : null,
            subject: isset($data['subject']) && is_string($data['subject']) ? $data['subject'] : null,
            text: isset($data['text']) && is_string($data['text']) ? $data['text'] : null,
            html: isset($data['html']) && is_string($data['html']) ? $data['html'] : null,
        );
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->status !== null) {
            $data['status'] = $this->status;
        }
        if ($this->start !== null) {
            $data['start'] = $this->start;
        }
        if ($this->end !== null) {
            $data['end'] = $this->end;
        }
        if ($this->name !== null) {
            $data['name'] = $this->name;
        }
        if ($this->subject !== null) {
            $data['subject'] = $this->subject;
        }
        if ($this->text !== null) {
            $data['text'] = $this->text;
        }
        if ($this->html !== null) {
            $data['html'] = $this->html;
        }

        return $data;
    }
}
