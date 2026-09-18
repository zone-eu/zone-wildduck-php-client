<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Message;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\Dto\Message\BulkUpdateMessagesRequestDto;

class BulkUpdateMessagesRequestDtoTest extends TestCase
{
    public function testToArrayOmitsUnsetThreadFlags(): void
    {
        $dto = new BulkUpdateMessagesRequestDto(message: '1,2', seen: true);

        $this->assertSame(
            ['message' => '1,2', 'seen' => true],
            $dto->toArray()
        );
    }

    public function testToArrayIncludesUpdateThreadAndUpdateThreadAllWhenSet(): void
    {
        $dto = new BulkUpdateMessagesRequestDto(
            message: '5:15',
            seen: true,
            updateThread: true,
            updateThreadAll: true,
        );

        $this->assertSame(
            [
                'message' => '5:15',
                'seen' => true,
                'updateThread' => true,
                'updateThreadAll' => true,
            ],
            $dto->toArray()
        );
    }

    public function testToArrayIncludesExplicitFalseUpdateThreadAll(): void
    {
        $dto = new BulkUpdateMessagesRequestDto(
            message: '1',
            updateThread: true,
            updateThreadAll: false,
        );

        $this->assertSame(
            [
                'message' => '1',
                'updateThread' => true,
                'updateThreadAll' => false,
            ],
            $dto->toArray()
        );
    }
}
