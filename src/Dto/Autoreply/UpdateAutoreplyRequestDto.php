<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Autoreply;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for updating autoreply information
 */
class UpdateAutoreplyRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?bool $status = null,
        public ?string $name = null,
        public ?string $subject = null,
        public ?string $text = null,
        public ?string $html = null,
        public string|false|null $start = null,
        public string|false|null $end = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->status !== null) {
            $data['status'] = $this->status;
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
        if ($this->start !== null) {
            $data['start'] = $this->start;
        }
        if ($this->end !== null) {
            $data['end'] = $this->end;
        }

        return $data;
    }
}
