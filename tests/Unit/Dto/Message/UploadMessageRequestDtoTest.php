<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Message;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\Dto\Message\UploadMessageRequestDto;

class UploadMessageRequestDtoTest extends TestCase
{
    public function testToArrayIncludesLabelsWhenSet(): void
    {
        $dto = new UploadMessageRequestDto(subject: 'Hello', labels: ['Work', 'Urgent']);

        $data = $dto->toArray();

        $this->assertSame(['Work', 'Urgent'], $data['labels']);
        $this->assertSame('Hello', $data['subject']);
    }

    public function testToArrayOmitsLabelsWhenUnset(): void
    {
        $dto = new UploadMessageRequestDto(subject: 'Hello');

        $this->assertArrayNotHasKey('labels', $dto->toArray());
    }
}
