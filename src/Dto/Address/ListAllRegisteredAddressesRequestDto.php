<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Address;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Dto\Traits\MetaDataSupportTrait;
use Zone\Wildduck\Dto\Traits\PaginatedRequestTrait;

/**
 * DTO for renaming a domain across all addresses
 */
readonly class ListAllRegisteredAddressesRequestDto implements RequestDtoInterface
{
    use PaginatedRequestTrait;
    use MetaDataSupportTrait;

    public function __construct(
        public ?string $query,
        public ?string $forward,
        public ?string $tags,
        public ?string $requiredTags,
        public ?bool $metaData,
        public ?bool $internalData,
        public ?int $limit = 20,
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
        if ($this->forward !== null) {
            $data['forward'] = $this->forward;
        }
        if ($this->tags !== null) {
            $data['tags'] = $this->tags;
        }
        if ($this->requiredTags !== null) {
            $data['requiredTags'] = $this->requiredTags;
        }

        return array_merge($data, $this->getPaginationArray(), $this->getMetaDataArray());
    }
}
