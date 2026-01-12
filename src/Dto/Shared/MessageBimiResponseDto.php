<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;

/**
 * BIMI (Brand Indicators for Message Identification) information
 */
readonly class MessageBimiResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public ?string $certified = null,
        public ?string $url = null,
        public ?string $image = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            certified: isset($data['certified']) && is_string($data['certified']) ? $data['certified'] : null,
            url: isset($data['url']) && is_string($data['url']) ? $data['url'] : null,
            image: isset($data['image']) && is_string($data['image']) ? $data['image'] : null,
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
