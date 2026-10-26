<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Shared;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\Dto\Shared\FilterActionResponseDto;

class FilterActionResponseDtoTest extends TestCase
{
    public function testFromObjectPassesLabelsThrough(): void
    {
        $dto = FilterActionResponseDto::fromObject([
            'seen' => true,
            'labels' => ['a1b2c3d4e5f6a7b8c9d0e1f2', 'a1b2c3d4e5f6a7b8c9d0e1f3'],
        ]);

        $this->assertSame(
            ['a1b2c3d4e5f6a7b8c9d0e1f2', 'a1b2c3d4e5f6a7b8c9d0e1f3'],
            $dto->labels
        );
    }

    public function testFromObjectLeavesLabelsNullWhenAbsent(): void
    {
        $dto = FilterActionResponseDto::fromObject([
            'seen' => true,
        ]);

        $this->assertNull($dto->labels);
    }

    public function testFromArrayToleratesApplyLabelsPairWithoutThrowing(): void
    {
        $dto = FilterActionResponseDto::fromArray([
            ['mark as read', true],
            ['apply labels', 'Work, Home'],
        ]);

        $this->assertTrue($dto->seen);
        $this->assertNull($dto->labels);
    }

    public function testFromArrayStillParsesExistingActions(): void
    {
        $dto = FilterActionResponseDto::fromArray([
            ['move to folder', 'INBOX.Spam'],
            ['forward to', 'a@example.com, b@example.com'],
        ]);

        $this->assertSame('INBOX.Spam', $dto->mailbox);
        $this->assertSame(['a@example.com', 'b@example.com'], $dto->targets);
        $this->assertNull($dto->labels);
    }
}
