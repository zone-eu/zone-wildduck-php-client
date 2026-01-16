<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Audit;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * DTO for creating an audit
 */
class CreateAuditRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string $user,
        public string $expires,
        public string|bool|null $start = null,
        public string|bool|null $end = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'user' => $this->user,
            'expires' => $this->expires,
        ];

        if ($this->start !== null) {
            $data['start'] = $this->start;
        }
        if ($this->end !== null) {
            $data['end'] = $this->end;
        }

        return $data;
    }
}
