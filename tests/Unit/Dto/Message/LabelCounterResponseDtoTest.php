<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Message;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\Dto\Message\LabelCounterResponseDto;
use Zone\Wildduck\Exception\DtoValidationException;

class LabelCounterResponseDtoTest extends TestCase
{
    public function testFromArrayMapsCounters(): void
    {
        $dto = LabelCounterResponseDto::fromArray([
            'success' => true,
            'label' => 'Work',
            'total' => 42,
            'unseen' => 7,
        ]);

        $this->assertSame('Work', $dto->label);
        $this->assertSame(42, $dto->total);
        $this->assertSame(7, $dto->unseen);
    }

    public function testFromArrayMapsZeroCounters(): void
    {
        $dto = LabelCounterResponseDto::fromArray([
            'success' => true,
            'label' => 'Empty',
            'total' => 0,
            'unseen' => 0,
        ]);

        $this->assertSame('Empty', $dto->label);
        $this->assertSame(0, $dto->total);
        $this->assertSame(0, $dto->unseen);
    }

    public function testFromArrayThrowsWhenLabelMissing(): void
    {
        $this->expectException(DtoValidationException::class);

        LabelCounterResponseDto::fromArray(['success' => true, 'total' => 1, 'unseen' => 1]);
    }

    public function testFromArrayThrowsWhenTotalMissing(): void
    {
        $this->expectException(DtoValidationException::class);

        LabelCounterResponseDto::fromArray(['success' => true, 'label' => 'Work', 'unseen' => 1]);
    }
}
