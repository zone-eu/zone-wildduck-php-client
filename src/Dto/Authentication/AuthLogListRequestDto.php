<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Authentication;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Dto\Traits\PaginatedRequestTrait;

/**
 * Request DTO for user authentication logs
 */
class AuthLogListRequestDto implements RequestDtoInterface
{
    use PaginatedRequestTrait;

    public function __construct(
        public ?string $action = null,
        public ?int $limit = null,
        public ?string $next = null,
        public ?string $previous = null,
        public ?string $filterIp = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->action !== null) {
            $data['action'] = $this->action;
        }
        if ($this->filterIp !== null) {
            $data['filterIp'] = $this->filterIp;
        }

        return array_merge($data, $this->getPaginationArray());
    }
}
