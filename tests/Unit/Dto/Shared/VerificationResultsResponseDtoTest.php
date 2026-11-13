<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\Shared;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\Dto\Shared\VerificationResultsResponseDto;

class VerificationResultsResponseDtoTest extends TestCase
{
    public function testFromArrayPassesDmarcObjectThrough(): void
    {
        $dto = VerificationResultsResponseDto::fromArray([
            'dkim' => 'pass',
            'spf' => 'pass',
            'dmarc' => ['domain' => 'x.fi', 'policy' => 'quarantine'],
        ]);

        $this->assertSame(['domain' => 'x.fi', 'policy' => 'quarantine'], $dto->dmarc);
    }

    public function testFromArrayNullsDmarcDomainWhenFalse(): void
    {
        $dto = VerificationResultsResponseDto::fromArray([
            'dmarc' => ['domain' => false],
        ]);

        $this->assertSame(['domain' => null], $dto->dmarc);
    }

    public function testFromArrayLeavesDmarcNullWhenFalse(): void
    {
        $dto = VerificationResultsResponseDto::fromArray([
            'dmarc' => false,
        ]);

        $this->assertNull($dto->dmarc);
    }

    public function testFromArrayLeavesDmarcNullWhenLegacyString(): void
    {
        $dto = VerificationResultsResponseDto::fromArray([
            'dmarc' => 'x.fi',
        ]);

        $this->assertNull($dto->dmarc);
    }

    public function testFromArrayLeavesDmarcNullWhenAbsent(): void
    {
        $dto = VerificationResultsResponseDto::fromArray([
            'spf' => 'pass',
        ]);

        $this->assertNull($dto->dmarc);
    }

    public function testFromArrayKeepsOtherFieldsUnchanged(): void
    {
        $dto = VerificationResultsResponseDto::fromArray([
            'dkim' => 'pass',
            'spf' => 'pass',
            'dmarc' => ['domain' => 'x.fi', 'policy' => 'reject'],
            'tls' => ['status' => 'valid', 'chain' => 'ok'],
        ]);

        $this->assertSame('pass', $dto->dkim);
        $this->assertSame('pass', $dto->spf);
        $this->assertSame(['domain' => 'x.fi', 'policy' => 'reject'], $dto->dmarc);
        $this->assertSame(['status' => 'valid', 'chain' => 'ok'], $dto->tls);
    }
}
