<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Message;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\Dto\Message\LabelResponseDto;
use Zone\Wildduck\Exception\DtoValidationException;

class LabelResponseDtoTest extends TestCase
{
    public function testFromArrayMapsMinimalLabel(): void
    {
        $dto = LabelResponseDto::fromArray([
            'id' => '66b94e6b5e04630001c2f3a4',
            'name' => 'Work',
        ]);

        $this->assertSame('66b94e6b5e04630001c2f3a4', $dto->id);
        $this->assertSame('Work', $dto->name);
        $this->assertNull($dto->metaData);
        $this->assertNull($dto->total);
        $this->assertNull($dto->unseen);
    }

    public function testFromArrayMapsLabelWithCountersAndMetaData(): void
    {
        $dto = LabelResponseDto::fromArray([
            'id' => '66b94e6b5e04630001c2f3a4',
            'name' => 'Work',
            'metaData' => ['color' => '#ff0000'],
            'total' => 12,
            'unseen' => 3,
        ]);

        $this->assertSame(['color' => '#ff0000'], $dto->metaData);
        $this->assertSame(12, $dto->total);
        $this->assertSame(3, $dto->unseen);
    }

    public function testFromArrayThrowsWhenIdMissing(): void
    {
        $this->expectException(DtoValidationException::class);

        LabelResponseDto::fromArray(['name' => 'Work']);
    }

    public function testFromArrayThrowsWhenNameMissing(): void
    {
        $this->expectException(DtoValidationException::class);

        LabelResponseDto::fromArray(['id' => '66b94e6b5e04630001c2f3a4']);
    }
}
