<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\ApplicationPassword;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * DTO for listing all user application passwords
 */
class ListAllUserApplicationPasswordRequestDto implements RequestDtoInterface
{
    public function __construct(
        public bool $showAll = false
    ) {}

    public function toArray(): array
    {
        $data = [
            'showAll' => $this->showAll,
        ];

        return $data;
    }
}
