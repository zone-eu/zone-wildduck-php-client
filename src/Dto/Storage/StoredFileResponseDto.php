<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Storage;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for stored file information
 */
readonly class StoredFileResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public string $id,
        public ?string $filename = null,
        public ?string $contentType = null,
        public ?int $size = null,
        public ?string $created = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'string');
        }

        return new self(
            id: $data['id'],
            filename: $data['filename'] ?? null,
            contentType: $data['contentType'] ?? null,
            size: $data['size'] ?? null,
            created: $data['created'] ?? null,
        );
    }
}
