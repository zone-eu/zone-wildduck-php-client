<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Shared;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\Dto\Shared\LimitQuotaResponseDto;
use Zone\Wildduck\Dto\Shared\UserLimitsResponseDto;
use Zone\Wildduck\Exception\DtoValidationException;

class UserLimitsResponseDtoTest extends TestCase
{
    public function testFromArrayMapsThePerUserLimitEntries(): void
    {
        $dto = UserLimitsResponseDto::fromArray([
            'apiDownload' => ['allowed' => 104857600, 'used' => 12345, 'ttl' => 42],
            'maxMailboxes' => ['allowed' => 50, 'used' => 5],
            'maxLabels' => ['allowed' => 100, 'used' => 2],
            'maxRecipientsPerMessage' => ['allowed' => 200],
            'maxApplicationPasswords' => ['allowed' => 10, 'used' => 1],
            'maxMcpTokens' => ['allowed' => 5, 'used' => 0],
            'maxAutoreplies' => ['allowed' => 3],
            'autoreplyInterval' => ['allowed' => 3600],
        ]);

        $this->assertSame(104857600, $dto->apiDownload->allowed);
        $this->assertSame(12345, $dto->apiDownload->used);
        $this->assertSame(42, $dto->apiDownload->ttl);
        $this->assertSame(50, $dto->maxMailboxes->allowed);
        $this->assertSame(5, $dto->maxMailboxes->used);
        $this->assertNull($dto->maxMailboxes->ttl);
        $this->assertSame(100, $dto->maxLabels->allowed);
        $this->assertSame(2, $dto->maxLabels->used);
        $this->assertSame(200, $dto->maxRecipientsPerMessage->allowed);
        $this->assertNull($dto->maxRecipientsPerMessage->used);
        $this->assertSame(10, $dto->maxApplicationPasswords->allowed);
        $this->assertSame(1, $dto->maxApplicationPasswords->used);
        $this->assertSame(5, $dto->maxMcpTokens->allowed);
        $this->assertSame(0, $dto->maxMcpTokens->used);
        $this->assertSame(3, $dto->maxAutoreplies->allowed);
        $this->assertNull($dto->maxAutoreplies->used);
        $this->assertSame(3600, $dto->autoreplyInterval->allowed);
    }

    public function testFromArrayPreservesFalseTtl(): void
    {
        $dto = UserLimitsResponseDto::fromArray([
            'apiDownload' => ['allowed' => 104857600, 'used' => 0, 'ttl' => false],
        ]);

        $this->assertFalse($dto->apiDownload->ttl);
    }

    public function testFromArrayDefaultsPerUserLimitEntriesToNullWhenAbsent(): void
    {
        $dto = UserLimitsResponseDto::fromArray([
            'quota' => ['allowed' => 1073741824, 'used' => 100],
        ]);

        $this->assertSame(1073741824, $dto->quota->allowed);
        $this->assertNull($dto->apiDownload);
        $this->assertNull($dto->maxMailboxes);
        $this->assertNull($dto->maxLabels);
        $this->assertNull($dto->maxRecipientsPerMessage);
        $this->assertNull($dto->maxApplicationPasswords);
        $this->assertNull($dto->maxMcpTokens);
        $this->assertNull($dto->maxAutoreplies);
        $this->assertNull($dto->autoreplyInterval);
    }

    public function testLimitQuotaResponseDtoRequiresAllowed(): void
    {
        $this->expectException(DtoValidationException::class);

        LimitQuotaResponseDto::fromArray(['used' => 5]);
    }

    public function testLimitQuotaResponseDtoRejectsWrongAllowedType(): void
    {
        $this->expectException(DtoValidationException::class);

        LimitQuotaResponseDto::fromArray(['allowed' => 'lots']);
    }
}
