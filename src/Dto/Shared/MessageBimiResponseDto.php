<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;

/**
 * BIMI (Brand Indicators for Message Identification) information
 */
readonly class MessageBimiResponseDto implements ResponseDtoInterface
{
    /**
     * @param "VMC"|"CMC"|null $type
     */
    public function __construct(
        public bool $certified,
        public string $url,
        public string $image,
        public ?string $type = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            certified: $data['certified'],
            url: $data['url'],
            image: $data['image'],
            type: isset($data['type']) && is_string($data['type']) ? $data['type'] : null,
        );
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->certified !== null) {
            $data['certified'] = $this->certified;
        }
        if ($this->url !== null) {
            $data['url'] = $this->url;
        }
        if ($this->image !== null) {
            $data['image'] = $this->image;
        }

        return $data;
    }
}
