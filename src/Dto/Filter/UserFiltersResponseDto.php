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
     * @param ListUserFiltersResponseDto[] $results List of user filters
     */
    public function __construct(
        public bool $success,
        public FilterLimitsResponseDto $limits,
        public array $results
    ) {}

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }
        if (!isset($data['limits'])) {
            throw DtoValidationException::missingRequiredField('limits', 'FilterLimitsResponseDto');
        }
        if (!isset($data['results'])) {
            throw DtoValidationException::missingRequiredField('results', 'ListUserFiltersResponseDto[]');
        }

        return new self(
            success: $data['success'],
            limits: FilterLimitsResponseDto::fromArray($data['limits']),
            results: array_map(function ($item) {
                return ListUserFiltersResponseDto::fromArray($item);
            }, $data['results']),
        );
    }
}
