<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Message;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\Dto\Message\BulkUpdateSearchActionsRequestDto;
use Zone\Wildduck\Dto\Message\SearchApplyMessagesRequestDto;

class SearchApplyMessagesRequestDtoTest extends TestCase
{
    private function action(): BulkUpdateSearchActionsRequestDto
    {
        return new BulkUpdateSearchActionsRequestDto(seen: true);
    }

    public function testToArrayOmitsLabelWhenUnset(): void
    {
        $dto = new SearchApplyMessagesRequestDto(action: $this->action(), q: 'hello', unseen: true);

        $this->assertSame(
            ['action' => ['seen' => true], 'q' => 'hello', 'unseen' => true],
            $dto->toArray()
        );
    }

    public function testToArrayIncludesLabelWhenSet(): void
    {
        $dto = new SearchApplyMessagesRequestDto(action: $this->action(), label: 'Work', flagged: true);

        $this->assertSame(
            ['action' => ['seen' => true], 'label' => 'Work', 'flagged' => true],
            $dto->toArray()
        );
    }
}
