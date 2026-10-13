<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Message;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for a label (list entry or single label resource)
 */
readonly class LabelResponseDto implements ResponseDtoInterface
{
    /**
     * @param array<string, mixed>|null $metaData Custom label metadata
     */
    public function __construct(
        public string $id,
        public string $name,
        public ?array $metaData = null,
        public ?int $total = null,
        public ?int $unseen = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'string');
        }
        if (!isset($data['name'])) {
            throw DtoValidationException::missingRequiredField('name', 'string');
        }
        if (!is_string($data['id'])) {
            throw DtoValidationException::invalidType('id', 'string', $data['id']);
        }
        if (!is_string($data['name'])) {
            throw DtoValidationException::invalidType('name', 'string', $data['name']);
        }

        return new self(
            id: $data['id'],
            name: $data['name'],
            metaData: isset($data['metaData']) && is_array($data['metaData']) ? $data['metaData'] : null,
            total: isset($data['total']) && is_int($data['total']) ? $data['total'] : null,
            unseen: isset($data['unseen']) && is_int($data['unseen']) ? $data['unseen'] : null,
        );
    }
}
