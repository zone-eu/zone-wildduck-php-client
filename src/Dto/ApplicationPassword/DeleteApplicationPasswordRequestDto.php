<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\ApplicationPassword;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * DTO for creating an application password
 */
class DeleteApplicationPasswordRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?string $protocol = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [
        ];

        if ($this->protocol !== null) {
            $data['protocol'] = $this->protocol;
        }

        return $data;
    }
}
