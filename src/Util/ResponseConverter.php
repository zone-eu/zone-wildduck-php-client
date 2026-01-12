<?php

declare(strict_types=1);

namespace Zone\Wildduck\Util;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Dto\PaginatedResultDto;
use Zone\Wildduck\Exception\UnexpectedValueException;

/**
 * Utility class for converting API responses to DTOs
 */
class ResponseConverter
{
    /**
     * Convert API response to DTO
     *
     * @template T of ResponseDtoInterface
     * @param array<string, mixed> $data
     * @param class-string<T> $dtoClass
     * @return T
     */
    public static function toDto(array $data, string $dtoClass): ResponseDtoInterface
    {
        return $dtoClass::fromArray($data);
    }

    /**
     * Convert paginated API response to PaginatedResultDto
     *
     * @template T of ResponseDtoInterface
     * @param array<string, mixed> $data
     * @param class-string<T> $itemClass
     * @return PaginatedResultDto<T>
     */
    public static function toPaginatedDto(array $data, string $itemClass): PaginatedResultDto
    {
        if (!isset($data['results']) || !is_array($data['results'])) {
            throw new UnexpectedValueException('Paginated response must contain "results" array');
        }

        $items = array_map(
            fn(array $item) => self::toDto($item, $itemClass),
            $data['results']
        );

        return new PaginatedResultDto(
            results: $items,
            total: $data['total'] ?? count($items),
            nextCursor: $data['nextCursor'] ?? null,
            previousCursor: $data['previousCursor'] ?? null,
            page: $data['page'] ?? 1,
        );
    }

    /**
     * Convert array of items to array of DTOs
     *
     * @template T of ResponseDtoInterface
     * @param array<array<string, mixed>> $items
     * @param class-string<T> $dtoClass
     * @return T[]
     */
    public static function toDtoArray(array $items, string $dtoClass): array
    {
        return array_map(
            fn(array $item) => self::toDto($item, $dtoClass),
            $items
        );
    }
}
