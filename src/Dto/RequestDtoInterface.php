<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto;

/**
 * Interface for request DTOs that need to be serialized for API calls.
 */
interface RequestDtoInterface
{
    /**
     * Convert DTO to array for API request payload.
     * Should filter out null values for optional fields.
     *
     * @return array The serialized array suitable for API requests
     */
    public function toArray(): array;
}
