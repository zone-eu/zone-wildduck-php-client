<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Export;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for creating a data export
 */
readonly class CreateExportRequestDto implements RequestDtoInterface
{
    /**
     * @param string[]|null $users List of user IDs to export
     * @param string[]|null $tags Filter users by tags
     */
    public function __construct(
        public ?array $users = null,
        public ?array $tags = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->users !== null) {
            $data['users'] = $this->users;
        }

        if ($this->tags !== null) {
            $data['tags'] = $this->tags;
        }

        return $data;
    }
}
