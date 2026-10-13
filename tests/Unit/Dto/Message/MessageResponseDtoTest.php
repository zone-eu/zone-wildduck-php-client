<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Message;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\Dto\Message\MessageResponseDto;

class MessageResponseDtoTest extends TestCase
{
    public function testFromArrayDefaultsLabelsToEmptyWhenAbsent(): void
    {
        $dto = MessageResponseDto::fromArray([
            'id' => 42,
            'mailbox' => '\\Inbox',
            'subject' => 'Hello',
        ]);

        $this->assertSame([], $dto->labels);
    }

    public function testFromArrayMapsLabels(): void
    {
        $dto = MessageResponseDto::fromArray([
            'id' => 7,
            'mailbox' => '\\Inbox',
            'subject' => 'Hello',
            'labels' => ['Work', 'Home > Kids'],
        ]);

        $this->assertSame(['Work', 'Home > Kids'], $dto->labels);
    }

    public function testFromArrayDoesNotDisturbExistingFields(): void
    {
        $dto = MessageResponseDto::fromArray([
            'id' => 7,
            'mailbox' => '\\Inbox',
            'user' => 'user@zone.eu',
            'subject' => 'Hello',
            'size' => 1234,
            'seen' => true,
            'flagged' => false,
            'labels' => ['Work'],
        ]);

        $this->assertSame(7, $dto->id);
        $this->assertSame('\\Inbox', $dto->mailbox);
        $this->assertSame('user@zone.eu', $dto->user);
        $this->assertSame('Hello', $dto->subject);
        $this->assertSame(1234, $dto->size);
        $this->assertTrue($dto->seen);
        $this->assertFalse($dto->flagged);
        $this->assertSame(['Work'], $dto->labels);
    }
}
