<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Dto\Traits\MetaDataSupportTrait;

/**
 * Request DTO for bulk updating messages
 */
class BulkUpdateMessagesRequestDto implements RequestDtoInterface
{
    use MetaDataSupportTrait;

    public function __construct(
        public string $message,
        public ?string $moveTo = null,
        public ?bool $seen = null,
        public ?bool $deleted = null,
        public ?bool $flagged = null,
        public ?bool $draft = null,
        public string|false|null $expires = null,
        /** @var array<string, mixed>|null Custom metadata */
        public ?array $metaData = null,
    ) {}

    public function toArray(): array
    {
        $data = ['message' => $this->message];

        if ($this->moveTo !== null) {
            $data['moveTo'] = $this->moveTo;
        }
        if ($this->seen !== null) {
            $data['seen'] = $this->seen;
        }
        if ($this->deleted !== null) {
            $data['deleted'] = $this->deleted;
        }
        if ($this->flagged !== null) {
            $data['flagged'] = $this->flagged;
        }
        if ($this->draft !== null) {
            $data['draft'] = $this->draft;
        }
        if ($this->expires !== null) {
            $data['expires'] = $this->expires;
        }

        return array_merge($data, $this->getMetaDataArray());
    }
}
