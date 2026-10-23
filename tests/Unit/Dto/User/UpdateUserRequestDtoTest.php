<?php

declare(strict_types=1);

namespace Tests\Unit\Dto\User;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\Dto\User\UpdateUserRequestDto;

class UpdateUserRequestDtoTest extends TestCase
{
    public function testToArrayIncludesSmimeFieldsWhenSet(): void
    {
        $dto = new UpdateUserRequestDto(
            smimeCerts: ["-----BEGIN CERTIFICATE-----\nabc123\n-----END CERTIFICATE-----"],
            smimeCipher: 'AES-GCM',
            smimeKeyTransport: 'OAEP',
        );

        $data = $dto->toArray();

        $this->assertSame(["-----BEGIN CERTIFICATE-----\nabc123\n-----END CERTIFICATE-----"], $data['smimeCerts']);
        $this->assertSame('AES-GCM', $data['smimeCipher']);
        $this->assertSame('OAEP', $data['smimeKeyTransport']);
    }

    public function testToArrayOmitsSmimeFieldsWhenNull(): void
    {
        $data = (new UpdateUserRequestDto())->toArray();

        $this->assertArrayNotHasKey('smimeCerts', $data);
        $this->assertArrayNotHasKey('smimeCipher', $data);
        $this->assertArrayNotHasKey('smimeKeyTransport', $data);
    }
}
