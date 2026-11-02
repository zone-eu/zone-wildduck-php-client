<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Request DTO for updating a label (name and/or metadata, preserving the id)
 */
class UpdateLabelRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?string $name = null,
        public ?array $metaData = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            ...($this->name !== null ? ['name' => $this->name] : []),
            ...($this->metaData !== null ? ['metaData' => $this->metaDataPayload()] : []),
        ];
    }

    /**
     * WildDuck stores a label's metadata as a JSON object. An empty payload must
     * serialize to {} (clearing the metadata) rather than the JSON array [].
     *
     * @return array<string, mixed>|\stdClass
     */
    private function metaDataPayload(): array|\stdClass
    {
        if ($this->metaData === null || count($this->metaData) === 0) {
            return new \stdClass();
        }

        return $this->metaData;
    }
}
