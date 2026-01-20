<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Filter;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Dto\Shared\FilterActionResponseDto;
use Zone\Wildduck\Dto\Shared\FilterQueryResponseDto;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for filter information
 */
readonly class FilterResponseDto implements ResponseDtoInterface
{
    /**
     * @param array<string, mixed>|null $metaData Custom metadata
     */
    public function __construct(
        public string $id,
        public string $created,
        public FilterQueryResponseDto $query,
        public FilterActionResponseDto $action,
        public bool $disabled,
        public ?string $name = null,
        public ?array $metaData = null,
    ) {}

    public static function fromArray(array $data): self
    {
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'string');
        }
        // if (!isset($data['name'])) {
        //     throw DtoValidationException::missingRequiredField('name', 'string');
        // }
        if (!isset($data['created'])) {
            throw DtoValidationException::missingRequiredField('created', 'string');
        }
        if (!isset($data['query'])) {
            throw DtoValidationException::missingRequiredField('query', 'array');
        }
        if (!isset($data['action'])) {
            throw DtoValidationException::missingRequiredField('action', 'array');
        }
        if (!isset($data['disabled'])) {
            throw DtoValidationException::missingRequiredField('disabled', 'bool');
        }

        return new self(
            id: $data['id'],
            name: $data['name'] ?? null,
            created: $data['created'],
            query: FilterQueryResponseDto::fromArray($data['query']),
            action: FilterActionResponseDto::fromArray($data['action']),
            disabled: $data['disabled'],
            metaData: $data['metaData'] ?? null,
        );
    }
}
