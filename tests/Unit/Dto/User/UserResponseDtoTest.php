<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\User;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\Dto\User\UserResponseDto;

class UserResponseDtoTest extends TestCase
{
    public function testFromArrayMapsSmimeFields(): void
    {
        $certificatePem = "-----BEGIN CERTIFICATE-----\nabc123\n-----END CERTIFICATE-----";

        $dto = UserResponseDto::fromArray([
            'id' => '6889fd51fe3341737ad095fe',
            'username' => 'ivan@example.com',
            'smimeCerts' => [$certificatePem],
            'smimeCertInfo' => [
                ['subject' => 'ivan@example.com', 'serial' => 'AA:BB', 'fingerprint' => 'AA:BB:CC'],
            ],
            'smimeCipher' => 'AES-CBC',
            'smimeKeyTransport' => 'PKCS#1 v1.5',
        ]);

        $this->assertSame([$certificatePem], $dto->smimeCerts);
        $this->assertSame([
            ['subject' => 'ivan@example.com', 'serial' => 'AA:BB', 'fingerprint' => 'AA:BB:CC'],
        ], $dto->smimeCertInfo);
        $this->assertSame('AES-CBC', $dto->smimeCipher);
        $this->assertSame('PKCS#1 v1.5', $dto->smimeKeyTransport);
    }

    public function testFromArrayDefaultsSmimeFieldsToNullWhenAbsent(): void
    {
        $dto = UserResponseDto::fromArray([
            'id' => '6889fd51fe3341737ad095fe',
            'username' => 'ivan@example.com',
        ]);

        $this->assertNull($dto->smimeCerts);
        $this->assertNull($dto->smimeCertInfo);
        $this->assertNull($dto->smimeCipher);
        $this->assertNull($dto->smimeKeyTransport);
    }
}
