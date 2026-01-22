<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Shared;

use Zone\Wildduck\Dto\ResponseDtoInterface;

/**
 * File attachment metadata
 */
readonly class FileResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public string $id,
        public string $filename,
        public string $contentType,
        public int $size,
        public ?string $cid = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            filename: $data['filename'] ?? '',
            contentType: $data['contentType'] ?? '',
            size: isset($data['size']) && is_int($data['size']) ? $data['size'] : 0,
            cid: $data['cid'] ?? null,
        );
    }
}
