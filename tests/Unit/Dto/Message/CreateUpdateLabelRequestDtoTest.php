<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Message;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\Dto\Message\CreateLabelRequestDto;
use Zone\Wildduck\Dto\Message\UpdateLabelRequestDto;

class CreateUpdateLabelRequestDtoTest extends TestCase
{
    public function testCreateLabelRequestSendsNameOnly(): void
    {
        $dto = new CreateLabelRequestDto(name: 'Work');

        $this->assertSame(['name' => 'Work'], $dto->toArray());
    }

    public function testCreateLabelRequestSendsMetaDataWhenPresent(): void
    {
        $dto = new CreateLabelRequestDto(name: 'Work', metaData: ['color' => '#e53935']);

        $this->assertSame(['name' => 'Work', 'metaData' => ['color' => '#e53935']], $dto->toArray());
    }

    public function testUpdateLabelRequestSendsNameOnly(): void
    {
        $dto = new UpdateLabelRequestDto(name: 'Family');

        $this->assertSame(['name' => 'Family'], $dto->toArray());
    }

    public function testUpdateLabelRequestSendsMetaDataOnly(): void
    {
        $dto = new UpdateLabelRequestDto(metaData: ['color' => '#1976d2']);

        $this->assertSame(['metaData' => ['color' => '#1976d2']], $dto->toArray());
    }

    public function testUpdateLabelRequestSendsEmptyMetaDataToClearIt(): void
    {
        $dto = new UpdateLabelRequestDto(metaData: []);

        $this->assertSame(['metaData' => []], $dto->toArray());
    }

    public function testUpdateLabelRequestSendsNameAndMetaData(): void
    {
        $dto = new UpdateLabelRequestDto(name: 'Family', metaData: ['color' => '#43a047']);

        $this->assertSame(['name' => 'Family', 'metaData' => ['color' => '#43a047']], $dto->toArray());
    }
}
