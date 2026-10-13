<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Message;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\Dto\Message\FlagCounterResponseDto;
use Zone\Wildduck\Exception\DtoValidationException;

class FlagCounterResponseDtoTest extends TestCase
{
    public function testFromArrayMapsFlaggedCounters(): void
    {
        $dto = FlagCounterResponseDto::fromArray([
            'success' => true,
            'flag' => 'flagged',
            'total' => 12,
            'unseen' => 4,
        ]);

        $this->assertSame('flagged', $dto->flag);
        $this->assertSame(12, $dto->total);
        $this->assertSame(4, $dto->unseen);
    }

    public function testFromArrayMapsUnreadCounters(): void
    {
        $dto = FlagCounterResponseDto::fromArray([
            'success' => true,
            'flag' => 'unread',
            'total' => 3,
            'unseen' => 3,
        ]);

        $this->assertSame('unread', $dto->flag);
        $this->assertSame(3, $dto->total);
        $this->assertSame(3, $dto->unseen);
    }

    public function testFromArrayThrowsWhenFlagMissing(): void
    {
        $this->expectException(DtoValidationException::class);

        FlagCounterResponseDto::fromArray(['success' => true, 'total' => 1, 'unseen' => 1]);
    }

    public function testFromArrayThrowsWhenUnseenMissing(): void
    {
        $this->expectException(DtoValidationException::class);

        FlagCounterResponseDto::fromArray(['success' => true, 'flag' => 'flagged', 'total' => 1]);
    }
}
