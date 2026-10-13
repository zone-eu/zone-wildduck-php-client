<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Message;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\Dto\Message\ListLabelsRequestDto;

class ListLabelsRequestDtoTest extends TestCase
{
    public function testToArraySendsCountersAndSearchable(): void
    {
        $dto = new ListLabelsRequestDto(counters: true, searchable: true);

        $this->assertSame(['counters' => true, 'searchable' => true], $dto->toArray());
    }

    public function testToArraySendsExplicitFalseCounters(): void
    {
        $dto = new ListLabelsRequestDto(counters: false, searchable: true);

        $this->assertSame(['counters' => false, 'searchable' => true], $dto->toArray());
    }
}
