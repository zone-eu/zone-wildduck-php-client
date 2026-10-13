<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Message;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\Dto\Message\FlagCounterRequestDto;
use Zone\Wildduck\Dto\Message\LabelCounterRequestDto;

class CounterRequestDtoTest extends TestCase
{
    public function testLabelCounterRequestSendsSearchableOnly(): void
    {
        $dto = new LabelCounterRequestDto(searchable: true);

        $this->assertSame(['searchable' => true], $dto->toArray());
    }

    public function testLabelCounterRequestSendsExplicitFalseSearchable(): void
    {
        $dto = new LabelCounterRequestDto(searchable: false);

        $this->assertSame(['searchable' => false], $dto->toArray());
    }

    public function testFlagCounterRequestSendsSearchableOnly(): void
    {
        $dto = new FlagCounterRequestDto(searchable: true);

        $this->assertSame(['searchable' => true], $dto->toArray());
    }
}
