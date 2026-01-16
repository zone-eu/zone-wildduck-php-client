<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Certs;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Dto\Traits\PaginatedRequestTrait;

/**
 * Request DTO for user authentication logs
 */
class CertificateListRequestDto implements RequestDtoInterface
{
    use PaginatedRequestTrait;

    public function __construct(
        public ?string $query = null,
        public ?bool $altNames = null,
        public ?int $limit = null,
        public ?string $next = null,
        public ?string $previous = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->query !== null) {
            $data['query'] = $this->query;
        }
        if ($this->altNames !== null) {
            $data['altNames'] = $this->altNames;
        }

        return array_merge($data, $this->getPaginationArray());
    }
}
