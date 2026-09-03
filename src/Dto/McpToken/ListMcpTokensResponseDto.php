<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\McpToken;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * List MCP access tokens response DTO
 */
readonly class ListMcpTokensResponseDto implements ResponseDtoInterface
{
    /**
     * @param list<McpTokenResponseDto> $results
     */
    public function __construct(
        public bool $success,
        public array $results,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }

        if (!isset($data['results'])) {
            throw DtoValidationException::missingRequiredField('results', 'array');
        }

        if (!is_array($data['results'])) {
            throw DtoValidationException::invalidType('results', 'array', $data['results']);
        }

        /** @var list<McpTokenResponseDto> */
        $results = [];
        foreach ($data['results'] as $index => $item) {
            if (!is_array($item)) {
                throw DtoValidationException::invalidType(
                    "results[{$index}]",
                    'array',
                    $item
                );
            }

            $results[] = McpTokenResponseDto::fromArray($item);
        }

        return new self(
            success: $data['success'],
            results: $results,
        );
    }
}
