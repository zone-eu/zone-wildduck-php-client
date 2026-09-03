<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\McpToken;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\Dto\McpToken\CreateMcpTokenRequestDto;
use Zone\Wildduck\Dto\McpToken\CreateMcpTokenResponseDto;
use Zone\Wildduck\Dto\McpToken\ListMcpTokensResponseDto;
use Zone\Wildduck\Dto\McpToken\McpTokenResponseDto;
use Zone\Wildduck\Exception\DtoValidationException;

class McpTokenDtoTest extends TestCase
{
    public function testCreateRequestToArraySendsDescriptionAndOmitsUnsetExpires(): void
    {
        $dto = new CreateMcpTokenRequestDto(description: 'Codex CLI');

        $this->assertSame(
            ['description' => 'Codex CLI'],
            $dto->toArray()
        );
    }

    public function testCreateRequestToArrayIncludesExpiresWhenSet(): void
    {
        $dto = new CreateMcpTokenRequestDto(
            description: 'Codex CLI',
            expires: '2026-12-31T23:59:59+00:00'
        );

        $this->assertSame(
            [
                'description' => 'Codex CLI',
                'expires' => '2026-12-31T23:59:59+00:00',
            ],
            $dto->toArray()
        );
    }

    public function testCreateResponseFromArray(): void
    {
        $dto = CreateMcpTokenResponseDto::fromArray([
            'success' => true,
            'id' => '671a9c4d3b5e8f2a1c0d4e5f',
            'token' => 'wdmcp_1' . str_repeat('a', 64) . '9838c218',
            'description' => 'Codex CLI',
            'role' => 'mcp:read',
            'audience' => 'mcp',
            'created' => '2026-09-03T10:00:00.000Z',
            'expires' => '2026-12-31T23:59:59.000Z',
        ]);

        $this->assertSame('671a9c4d3b5e8f2a1c0d4e5f', $dto->id);
        $this->assertSame('wdmcp_1' . str_repeat('a', 64) . '9838c218', $dto->token);
        $this->assertSame('Codex CLI', $dto->description);
        $this->assertSame('mcp:read', $dto->role);
        $this->assertSame('mcp', $dto->audience);
        $this->assertSame('2026-09-03T10:00:00.000Z', $dto->created);
        $this->assertSame('2026-12-31T23:59:59.000Z', $dto->expires);
    }

    public function testCreateResponseFromArrayLeavesExpiresNullWhenAbsent(): void
    {
        $dto = CreateMcpTokenResponseDto::fromArray([
            'success' => true,
            'id' => '671a9c4d3b5e8f2a1c0d4e5f',
            'token' => 'wdmcp_1' . str_repeat('a', 64) . '9838c218',
            'description' => 'Codex CLI',
            'role' => 'mcp:read',
            'audience' => 'mcp',
            'created' => '2026-09-03T10:00:00.000Z',
        ]);

        $this->assertNull($dto->expires);
    }

    public function testCreateResponseFromArrayRejectsMissingToken(): void
    {
        $this->expectException(DtoValidationException::class);

        CreateMcpTokenResponseDto::fromArray([
            'success' => true,
            'id' => '671a9c4d3b5e8f2a1c0d4e5f',
            'description' => 'Codex CLI',
            'role' => 'mcp:read',
            'audience' => 'mcp',
            'created' => '2026-09-03T10:00:00.000Z',
        ]);
    }

    public function testCreateResponseFromArrayRejectsMissingId(): void
    {
        $this->expectException(DtoValidationException::class);

        CreateMcpTokenResponseDto::fromArray([
            'success' => true,
            'token' => 'wdmcp_1' . str_repeat('a', 64) . '9838c218',
            'description' => 'Codex CLI',
            'role' => 'mcp:read',
            'audience' => 'mcp',
            'created' => '2026-09-03T10:00:00.000Z',
        ]);
    }

    public function testCreateResponseFromArrayRejectsWrongType(): void
    {
        $this->expectException(DtoValidationException::class);

        CreateMcpTokenResponseDto::fromArray([
            'success' => true,
            'id' => 42,
            'token' => 'wdmcp_1' . str_repeat('a', 64) . '9838c218',
            'description' => 'Codex CLI',
            'role' => 'mcp:read',
            'audience' => 'mcp',
            'created' => '2026-09-03T10:00:00.000Z',
        ]);
    }

    public function testTokenResponseFromArray(): void
    {
        $dto = McpTokenResponseDto::fromArray([
            'id' => '671a9c4d3b5e8f2a1c0d4e5f',
            'description' => 'Codex CLI',
            'role' => 'mcp:read',
            'audience' => 'mcp',
            'created' => '2026-09-03T10:00:00.000Z',
            'expires' => '2026-12-31T23:59:59.000Z',
            'lastUse' => '2026-09-02T08:30:00.000Z',
        ]);

        $this->assertSame('671a9c4d3b5e8f2a1c0d4e5f', $dto->id);
        $this->assertSame('Codex CLI', $dto->description);
        $this->assertSame('mcp:read', $dto->role);
        $this->assertSame('mcp', $dto->audience);
        $this->assertSame('2026-09-03T10:00:00.000Z', $dto->created);
        $this->assertSame('2026-12-31T23:59:59.000Z', $dto->expires);
        $this->assertSame('2026-09-02T08:30:00.000Z', $dto->lastUse);
    }

    public function testTokenResponseFromArrayLeavesOptionalFieldsNullWhenAbsent(): void
    {
        $dto = McpTokenResponseDto::fromArray([
            'id' => '671a9c4d3b5e8f2a1c0d4e5f',
            'description' => 'Codex CLI',
            'role' => 'mcp:read',
            'audience' => 'mcp',
            'created' => '2026-09-03T10:00:00.000Z',
        ]);

        $this->assertNull($dto->expires);
        $this->assertNull($dto->lastUse);
    }

    public function testTokenResponseFromArrayRejectsMissingRequiredField(): void
    {
        $this->expectException(DtoValidationException::class);

        McpTokenResponseDto::fromArray([
            'id' => '671a9c4d3b5e8f2a1c0d4e5f',
            'description' => 'Codex CLI',
            'role' => 'mcp:read',
            'created' => '2026-09-03T10:00:00.000Z',
        ]);
    }

    public function testListResponseFromArrayMapsResults(): void
    {
        $dto = ListMcpTokensResponseDto::fromArray([
            'success' => true,
            'results' => [
                [
                    'id' => 'newer',
                    'description' => 'Newer token',
                    'role' => 'mcp:read',
                    'audience' => 'mcp',
                    'created' => '2026-09-03T11:00:00.000Z',
                ],
                [
                    'id' => 'older',
                    'description' => 'Older token',
                    'role' => 'mcp:read',
                    'audience' => 'mcp',
                    'created' => '2026-09-01T09:00:00.000Z',
                    'expires' => '2026-09-30T23:59:59.000Z',
                ],
            ],
        ]);

        $this->assertTrue($dto->success);
        $this->assertCount(2, $dto->results);
        $this->assertSame('newer', $dto->results[0]->id);
        $this->assertNull($dto->results[0]->expires);
        $this->assertSame('2026-09-30T23:59:59.000Z', $dto->results[1]->expires);
    }

    public function testListResponseFromArrayAcceptsEmptyResults(): void
    {
        $dto = ListMcpTokensResponseDto::fromArray([
            'success' => true,
            'results' => [],
        ]);

        $this->assertTrue($dto->success);
        $this->assertSame([], $dto->results);
    }

    public function testListResponseFromArrayRejectsMissingResults(): void
    {
        $this->expectException(DtoValidationException::class);

        ListMcpTokensResponseDto::fromArray([
            'success' => true,
        ]);
    }

    public function testListResponseFromArrayRejectsMissingSuccess(): void
    {
        $this->expectException(DtoValidationException::class);

        ListMcpTokensResponseDto::fromArray([
            'results' => [],
        ]);
    }

    public function testListResponseFromArrayRejectsNonArrayResultItem(): void
    {
        $this->expectException(DtoValidationException::class);

        ListMcpTokensResponseDto::fromArray([
            'success' => true,
            'results' => ['not-an-object'],
        ]);
    }
}
