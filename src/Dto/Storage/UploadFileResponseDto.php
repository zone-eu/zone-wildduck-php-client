<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Storage;

use Zone\Wildduck\Dto\ResponseDtoInterface;

/**
 * Response DTO for file upload
 */
final readonly class UploadFileResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public bool $success,
        public string $id,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'],
            id: $data['id'],
        );
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'id' => $this->id,
        ];
    }
}
