<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Filter;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Dto\Shared\FilterActionRequestDto;
use Zone\Wildduck\Dto\Shared\FilterQueryRequestDto;
use Zone\Wildduck\Dto\Traits\MetaDataSupportTrait;

/**
 * Request DTO for creating a new filter
 */
readonly class CreateFilterRequestDto implements RequestDtoInterface
{
    use MetaDataSupportTrait;

    public function __construct(
        public FilterQueryRequestDto $query,
        public FilterActionRequestDto $action,
        public ?string $name = null,
        public bool $disabled = false,
        /** @var array<string, mixed>|null Custom metadata */ public ?array $metaData = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'query' => $this->query->toArray(),
            'action' => $this->action->toArray(),
            'disabled' => $this->disabled,
        ];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        return array_merge($data, $this->getMetaDataArray());
    }
}
