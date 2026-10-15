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

    /**
     * @param string[]|null $labels Custom labels to set on the message. Replaces all existing custom labels. System flags are not affected.
     * @param string[]|null $addLabels Custom labels to add to the message without affecting existing labels.
     * @param string[]|null $removeLabels Custom labels to remove from the message without affecting other labels.
     * @param array<string, mixed>|null $metaData Custom metadata
     */
    public function __construct(
        public string $message,
        public ?string $moveTo = null,
        public ?bool $seen = null,
        public ?bool $deleted = null,
        public ?bool $flagged = null,
        public ?bool $draft = null,
        public ?bool $updateThread = null,
        public ?bool $updateThreadAll = null,
        public string|false|null $expires = null,
        public ?array $labels = null,
        public ?array $addLabels = null,
        public ?array $removeLabels = null,
        public ?array $metaData = null,
    ) {
    }

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
        if ($this->updateThread !== null) {
            $data['updateThread'] = $this->updateThread;
        }
        if (!!$this->updateThread && $this->updateThreadAll !== null) {
            $data['updateThreadAll'] = $this->updateThreadAll;
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
