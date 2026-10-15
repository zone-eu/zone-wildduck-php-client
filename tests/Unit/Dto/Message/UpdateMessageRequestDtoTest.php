<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Message;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\Dto\Message\UpdateMessageRequestDto;

class UpdateMessageRequestDtoTest extends TestCase
{
    public function testToArrayIncludesLabelFieldsWhenSet(): void
    {
        $dto = new UpdateMessageRequestDto(
            seen: true,
            labels: ['Work'],
            addLabels: ['New'],
            removeLabels: ['Done'],
        );

        $this->assertSame(
            [
                'seen' => true,
                'labels' => ['Work'],
                'addLabels' => ['New'],
                'removeLabels' => ['Done'],
            ],
            $dto->toArray()
        );
    }

    public function testToArrayOmitsLabelFieldsWhenUnset(): void
    {
        $dto = new UpdateMessageRequestDto(seen: true);

        $this->assertSame(['seen' => true], $dto->toArray());
    }
}
