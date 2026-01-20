<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\Filter;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Dto\Shared\FilterLimitsResponseDto;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Response DTO for filter information
 */
readonly class UserFiltersResponseDto implements ResponseDtoInterface
{
    /**
     * @param FilterResponseDto[] $results List of user filters
     */
    public function __construct(
        public bool $success,
        public FilterLimitsResponseDto $limits,
        public array $results
    ) {}

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'string');
        }
        if (!isset($data['limits'])) {
            throw DtoValidationException::missingRequiredField('limits', 'FilterLimitsResponseDto');
        }
        if (!isset($data['results'])) {
            throw DtoValidationException::missingRequiredField('results', 'FilterResponseDto[]');
        }

        return new self(
            success: $data['success'],
            limits: FilterLimitsResponseDto::fromArray($data['limits']),
            results: array_map(function ($item) {
                return FilterResponseDto::fromArray($item);
            }, $data['results']),
        );
    }
}
