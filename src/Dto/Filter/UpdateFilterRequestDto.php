<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Filter;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Dto\Shared\FilterActionRequestDto;
use Zone\Wildduck\Dto\Shared\FilterQueryRequestDto;
use Zone\Wildduck\Dto\Traits\MetaDataSupportTrait;

/**
 * Request DTO for updating a filter
 */
readonly class UpdateFilterRequestDto implements RequestDtoInterface
{
    use MetaDataSupportTrait;

    public function __construct(
        public ?string $name = null,
        public ?FilterQueryRequestDto $query = null,
        public ?FilterActionRequestDto $action = null,
        public ?bool $disabled = null,
        /** @var array<string, mixed>|null Custom metadata */ public ?array $metaData = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }
        if ($this->query !== null) {
            $data['query'] = $this->query->toArray();
        }
        if ($this->action !== null) {
            $data['action'] = $this->action->toArray();
        }
        if ($this->disabled !== null) {
            $data['disabled'] = $this->disabled;
        }

        return array_merge($data, $this->getMetaDataArray());
    }
}
