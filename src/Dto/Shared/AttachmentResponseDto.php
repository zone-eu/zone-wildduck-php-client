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
        public string $hash,
        public string $filename,
        public string $contentType,
        /** @param 'inline'|'attachment' $disposition */
        public string $disposition,
        public string $transferEncoding,
        public bool $related,
        public int $sizeKb,
        public int $size,
        public ?string $cid = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            hash: $data['hash'] ?? '',
            cid: isset($data['cid']) && is_string($data['cid']) ? $data['cid'] : null,
            filename: isset($data['filename']) && is_string($data['filename']) ? $data['filename'] : '',
            contentType: isset($data['contentType']) && is_string($data['contentType']) ? $data['contentType'] : '',
            disposition: isset($data['disposition']) && is_string($data['disposition']) ? $data['disposition'] : '',
            transferEncoding: isset($data['transferEncoding']) && is_string($data['transferEncoding']) ? $data['transferEncoding'] : '',
            related: isset($data['related']) && is_bool($data['related']) ? $data['related'] : false,
            sizeKb: isset($data['sizeKb']) && is_int($data['sizeKb']) ? $data['sizeKb'] : 0,
            size: isset($data['size']) && is_int($data['size']) ? $data['size'] : 0,
        );
    }
}
