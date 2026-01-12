<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Address;

use Zone\Wildduck\Dto\Autoreply\UpdateAutoreplyRequestDto;
use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Dto\Traits\MetaDataSupportTrait;
use Zone\Wildduck\Dto\Traits\TaggableTrait;

/**
 * DTO for updating a forwarded address
 */
readonly class UpdateForwardedAddressRequestDto implements RequestDtoInterface
{
    use MetaDataSupportTrait;
    use TaggableTrait;

    /**
     * @param string[] $targets
     * @param string[] $tags
     */
    public function __construct(
        public string $address,
        public ?string $name = null,
        public ?array $targets = null,
        public ?int $forwards = null,
        public ?UpdateAutoreplyRequestDto $autoreply = null,
        public array $tags = [],
        /** @var array<string, mixed>|null Custom metadata */ public ?array $metaData = null,
        /** @var array<string, mixed>|null Internal data */ public ?array $internalData = null,
        public ?bool $forwardedDisabled = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'address' => $this->address,
        ];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }
        if ($this->targets !== null) {
            $data['targets'] = $this->targets;
        }
        if ($this->forwards !== null) {
            $data['forwards'] = $this->forwards;
        }
        if ($this->autoreply !== null) {
            $data['autoreply'] = $this->autoreply->toArray();
        }
        if ($this->forwardedDisabled !== null) {
            $data['forwardedDisabled'] = $this->forwardedDisabled;
        }

        return array_merge($data, $this->getMetaDataArray(), $this->getTagsArray());
    }
}
