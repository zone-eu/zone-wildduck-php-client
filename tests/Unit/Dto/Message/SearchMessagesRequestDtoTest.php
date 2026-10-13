<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Message;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\Dto\Message\SearchMessagesRequestDto;

class SearchMessagesRequestDtoTest extends TestCase
{
    public function testToArrayOmitsLabelWhenUnset(): void
    {
        $dto = new SearchMessagesRequestDto(q: 'hello', unseen: true);

        $this->assertSame(['q' => 'hello', 'unseen' => true], $dto->toArray());
    }

    public function testToArrayIncludesLabelNameWhenSet(): void
    {
        $dto = new SearchMessagesRequestDto(q: 'hello', label: 'Work');

        $this->assertSame(['q' => 'hello', 'label' => 'Work'], $dto->toArray());
    }

    public function testToArrayIncludesLabelIdWhenSet(): void
    {
        $dto = new SearchMessagesRequestDto(label: '66b94e6b5e04630001c2f3a4', flagged: true);

        $this->assertSame(['label' => '66b94e6b5e04630001c2f3a4', 'flagged' => true], $dto->toArray());
    }
}
