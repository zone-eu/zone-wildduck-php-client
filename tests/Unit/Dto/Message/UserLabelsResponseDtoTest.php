<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Message;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\Dto\Message\LabelResponseDto;
use Zone\Wildduck\Dto\Message\UserLabelsResponseDto;
use Zone\Wildduck\Exception\DtoValidationException;

class UserLabelsResponseDtoTest extends TestCase
{
    public function testFromArrayMapsLabelsWithCountersWhenRequested(): void
    {
        $dto = UserLabelsResponseDto::fromArray([
            'success' => true,
            'labels' => [
                ['id' => 'a1', 'name' => 'Work', 'total' => 5, 'unseen' => 2],
                ['id' => 'b2', 'name' => 'Family'],
            ],
        ]);

        $this->assertTrue($dto->success);
        $this->assertCount(2, $dto->labels);
        $this->assertInstanceOf(LabelResponseDto::class, $dto->labels[0]);
        $this->assertInstanceOf(LabelResponseDto::class, $dto->labels[1]);
        $this->assertSame('Work', $dto->labels[0]->name);
        $this->assertSame(5, $dto->labels[0]->total);
        $this->assertSame(2, $dto->labels[0]->unseen);
        $this->assertNull($dto->labels[1]->total);
    }

    public function testFromArrayMapsEmptyLabelList(): void
    {
        $dto = UserLabelsResponseDto::fromArray(['success' => true, 'labels' => []]);

        $this->assertSame([], $dto->labels);
    }

    public function testFromArrayThrowsWhenSuccessMissing(): void
    {
        $this->expectException(DtoValidationException::class);

        UserLabelsResponseDto::fromArray(['labels' => []]);
    }

    public function testFromArrayThrowsWhenLabelsMissing(): void
    {
        $this->expectException(DtoValidationException::class);

        UserLabelsResponseDto::fromArray(['success' => true]);
    }

    public function testFromArrayThrowsWhenLabelsIsNotArray(): void
    {
        $this->expectException(DtoValidationException::class);

        UserLabelsResponseDto::fromArray(['success' => true, 'labels' => 'Work']);
    }

    public function testFromArrayThrowsWhenLabelEntryIsNotArray(): void
    {
        $this->expectException(DtoValidationException::class);

        UserLabelsResponseDto::fromArray(['success' => true, 'labels' => ['a1']]);
    }
}
