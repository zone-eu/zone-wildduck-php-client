<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * Attachment reference DTO for message submission
 */
readonly class AttachmentReferenceRequestDto implements RequestDtoInterface
{
    public function __construct(
        public ?string $id = null,
        public ?string $content = null,
        public ?string $filename = null,
        public ?string $contentType = null,
        public ?string $encoding = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'content' => $this->content,
            'filename' => $this->filename,
            'contentType' => $this->contentType,
            'encoding' => $this->encoding,
        ], fn($value) => $value !== null);
    }
}
