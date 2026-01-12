<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;

/**
 * Message attachment information
 */
readonly class AttachmentResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public string $id,
        public ?string $filename = null,
        public ?string $contentType = null,
        public ?string $disposition = null,
        public ?string $transferEncoding = null,
        public ?bool $related = null,
        public ?int $sizeKb = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            filename: isset($data['filename']) && is_string($data['filename']) ? $data['filename'] : null,
            contentType: isset($data['contentType']) && is_string($data['contentType']) ? $data['contentType'] : null,
            disposition: isset($data['disposition']) && is_string($data['disposition']) ? $data['disposition'] : null,
            transferEncoding: isset($data['transferEncoding']) && is_string($data['transferEncoding']) ? $data['transferEncoding'] : null,
            related: isset($data['related']) && is_bool($data['related']) ? $data['related'] : null,
            sizeKb: isset($data['sizeKb']) && is_int($data['sizeKb']) ? $data['sizeKb'] : null,
        );
    }
}
