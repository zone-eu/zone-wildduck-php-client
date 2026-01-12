<?php

declare(strict_types=1);

namespace Zone\Wildduck\Exception;

use InvalidArgumentException;

/**
 * Exception thrown when DTO validation fails during construction or fromArray() calls.
 * Provides structured error information about which field failed validation and why.
 */
class DtoValidationException extends InvalidArgumentException
{
    public function __construct(
        public readonly string $fieldName,
        public readonly string $expectedType,
        public readonly mixed $actualValue,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        if ($message === '') {
            $actualType = get_debug_type($actualValue);
            $message = sprintf(
                'DTO validation failed for field "%s": expected %s, got %s',
                $fieldName,
                $expectedType,
                $actualType
            );
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * Create exception for missing required field
     */
    public static function missingRequiredField(string $fieldName, string $expectedType): self
    {
        return new self(
            $fieldName,
            $expectedType,
            null,
            sprintf('Missing required field "%s" (expected: %s)', $fieldName, $expectedType)
        );
    }

    /**
     * Create exception for invalid field type
     */
    public static function invalidType(string $fieldName, string $expectedType, mixed $actualValue): self
    {
        return new self($fieldName, $expectedType, $actualValue);
    }

    /**
     * Create exception for invalid array structure
     */
    public static function invalidArrayStructure(string $context, string $reason): self
    {
        return new self(
            $context,
            'valid array structure',
            null,
            sprintf('Invalid array structure in %s: %s', $context, $reason)
        );
    }
}
