<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto;

use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Base interface for all DTOs ensuring consistent factory method pattern.
 * All DTOs must implement fromArray() for construction from API responses.
 */
interface ResponseDtoInterface
{
    /**
     * Create DTO instance from array data (typically from API response).
     *
     * @param array $data The data array to construct from
     * @return self The constructed DTO instance
     * @throws DtoValidationException When required fields are missing or invalid
     */
    public static function fromArray(array $data): self;
}
