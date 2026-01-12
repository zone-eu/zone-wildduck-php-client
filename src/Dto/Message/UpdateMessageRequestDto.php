<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Dto\Traits\MetaDataSupportTrait;

/**
 * Request DTO for updating message information
 */
readonly class UpdateMessageRequestDto implements RequestDtoInterface
{
    use MetaDataSupportTrait;

    public function __construct(
        public ?string $mailbox = null,
        public ?bool $seen = null,
        public ?bool $flagged = null,
        public ?bool $draft = null,
        public ?string $expires = null,
        /** @var array<string, mixed>|null Custom metadata */ public ?array $metaData = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [];

        if ($this->mailbox !== null) {
            $data['mailbox'] = $this->mailbox;
        }
        if ($this->seen !== null) {
            $data['seen'] = $this->seen;
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
