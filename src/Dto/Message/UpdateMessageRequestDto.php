<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Dto\Traits\MetaDataSupportTrait;

/**
 * Request DTO for updating message information
 */
class UpdateMessageRequestDto implements RequestDtoInterface
{
    use MetaDataSupportTrait;

    public function __construct(
        public ?string $mailbox = null,
        public ?bool $seen = null,
        public ?bool $flagged = null,
        public ?bool $draft = null,
        public ?string $expires = null,
        /** @var string[]|null Custom labels to set on the message, overrides any existing labels */
        public ?array $labels = null,
        /** @var string[]|null Custom labels to add to the message without affecting existing labels, can not be used together with `labels` */
        public ?array $addLabels = null,
        /** @var string[]|null Custom labels to remove from the message without affecting other labels, can not be used together with `labels` */
        public ?array $removeLabels = null,
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
        if ($this->labels !== null) {
            $data['labels'] = $this->labels;
        }
        if ($this->addLabels !== null) {
            $data['addLabels'] = $this->addLabels;
        }
        if ($this->removeLabels !== null) {
            $data['removeLabels'] = $this->removeLabels;
        }

        return array_merge($data, $this->getMetaDataArray());
    }
}
