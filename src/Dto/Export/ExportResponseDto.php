<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Export;

use Zone\Wildduck\Dto\ResponseDtoInterface;

/**
 * Response DTO for export operations
 */
readonly class ExportResponseDto implements ResponseDtoInterface
{
    public function __construct(
        public bool $success,
        public ?string $export = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'] ?? true,
            export: $data['export'] ?? null,
        );
    }
}
