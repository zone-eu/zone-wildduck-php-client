<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\McpToken;

use Zone\Wildduck\Dto\RequestDtoInterface;

/**
 * DTO for creating an MCP access token
 */
class CreateMcpTokenRequestDto implements RequestDtoInterface
{
    public function __construct(
        public string $description,
        public ?string $expires = null,
    ) {
    }

    public function toArray(): array
    {
        $data = [
            'description' => $this->description,
        ];

        if ($this->expires !== null) {
            $data['expires'] = $this->expires;
        }

        return $data;
    }
}
