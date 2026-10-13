<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Message;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\Dto\Message\CreateLabelRequestDto;
use Zone\Wildduck\Dto\Message\RenameLabelRequestDto;

class CreateRenameLabelRequestDtoTest extends TestCase
{
    public function testCreateLabelRequestSendsNameOnly(): void
    {
        $dto = new CreateLabelRequestDto(name: 'Work');

        $this->assertSame(['name' => 'Work'], $dto->toArray());
    }

    public function testRenameLabelRequestSendsNameOnly(): void
    {
        $dto = new RenameLabelRequestDto(name: 'Family');

        $this->assertSame(['name' => 'Family'], $dto->toArray());
    }
}
