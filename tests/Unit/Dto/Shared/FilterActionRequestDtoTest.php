<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Shared;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\Dto\Shared\FilterActionRequestDto;

class FilterActionRequestDtoTest extends TestCase
{
    public function testToArrayIncludesLabelsWhenSet(): void
    {
        $dto = new FilterActionRequestDto(seen: true, labels: ['Work', 'Urgent']);

        $this->assertSame(
            ['seen' => true, 'spam' => null, 'labels' => ['Work', 'Urgent']],
            $dto->toArray()
        );
    }

    public function testToArrayOmitsLabelsWhenUnset(): void
    {
        $dto = new FilterActionRequestDto(seen: true);

        $this->assertSame(['seen' => true, 'spam' => null], $dto->toArray());
    }
}
