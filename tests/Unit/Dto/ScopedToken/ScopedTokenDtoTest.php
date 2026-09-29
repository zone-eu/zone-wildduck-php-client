<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\ScopedToken;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\Dto\ScopedToken\CreateScopedTokenResponseDto;
use Zone\Wildduck\Exception\DtoValidationException;

class ScopedTokenDtoTest extends TestCase
{
    private const string SESSION_ID = '9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08';

    public function testCreateScopedTokenResponseFromArray(): void
    {
        $dto = CreateScopedTokenResponseDto::fromArray([
            'success' => true,
            'scope' => 'mcp',
            'id' => self::SESSION_ID,
            'token' => 'wdmcp_1' . str_repeat('a', 64) . '9838c218',
        ]);

        $this->assertTrue($dto->success);
        $this->assertSame('mcp', $dto->scope);
        $this->assertSame(self::SESSION_ID, $dto->id);
        $this->assertSame('wdmcp_1' . str_repeat('a', 64) . '9838c218', $dto->token);
    }

    public function testCreateScopedTokenResponseFromArrayRejectsMissingSuccess(): void
    {
        $this->expectException(DtoValidationException::class);

        CreateScopedTokenResponseDto::fromArray([
            'scope' => 'mcp',
            'id' => self::SESSION_ID,
            'token' => 'wdmcp_1' . str_repeat('a', 64) . '9838c218',
        ]);
    }

    public function testCreateScopedTokenResponseFromArrayRejectsMissingScope(): void
    {
        $this->expectException(DtoValidationException::class);

        CreateScopedTokenResponseDto::fromArray([
            'success' => true,
            'id' => self::SESSION_ID,
            'token' => 'wdmcp_1' . str_repeat('a', 64) . '9838c218',
        ]);
    }

    public function testCreateScopedTokenResponseFromArrayRejectsMissingId(): void
    {
        $this->expectException(DtoValidationException::class);

        CreateScopedTokenResponseDto::fromArray([
            'success' => true,
            'scope' => 'mcp',
            'token' => 'wdmcp_1' . str_repeat('a', 64) . '9838c218',
        ]);
    }

    public function testCreateScopedTokenResponseFromArrayRejectsMissingToken(): void
    {
        $this->expectException(DtoValidationException::class);

        CreateScopedTokenResponseDto::fromArray([
            'success' => true,
            'scope' => 'mcp',
            'id' => self::SESSION_ID,
        ]);
    }

    public function testCreateScopedTokenResponseFromArrayRejectsWrongType(): void
    {
        $this->expectException(DtoValidationException::class);

        CreateScopedTokenResponseDto::fromArray([
            'success' => true,
            'scope' => 'mcp',
            'id' => 42,
            'token' => 'wdmcp_1' . str_repeat('a', 64) . '9838c218',
        ]);
    }
}
