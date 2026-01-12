<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\ApplicationPassword;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * DTO for listing all user application passwords
 */
readonly class ListAllUserApplicationPasswordRequestDto implements RequestDtoInterface
{
    public function __construct(
        public bool $showAll
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'showAll' => $this->showAll,
        ];

        return $data;
    }
}
