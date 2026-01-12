<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\Certs\CreateOrUpdateCertificateRequestDto;
use Zone\Wildduck\Dto\Certs\CertificateCreateOrUpdateResponseDto;
use Zone\Wildduck\Dto\Certs\CertificateInformationResponseDto;
use Zone\Wildduck\Dto\Certs\CertificateResolveResponseDto;
use Zone\Wildduck\Dto\Certs\CertificateListRequestDto;

class CertsServiceIntegrationTest extends IntegrationTestCase
{
    private ?string $createdCertId = null;
    private string $testServername = 'updates.zone.test';
    private string $certPath = __DIR__ . '/../Fixtures/cert.pem';
    private string $privateKeyPath = __DIR__ . '/../Fixtures/privkey.pem';

    protected function tearDown(): void
    {
        if ($this->createdCertId !== null) {
            try {
                $this->client->certs()->delete($this->createdCertId);
            } catch (\Exception $e) {
                // Ignore cleanup errors
            }
            $this->createdCertId = null;
        }

        parent::tearDown();
    }

    public function testCertificateLifecycle(): void
    {
        // CREATE - Create certificate
        $createDto = new CreateOrUpdateCertificateRequestDto(
            servername: $this->testServername,
            description: 'Test certificate for integration testing',
            cert: file_get_contents($this->certPath),
            privateKey: file_get_contents($this->privateKeyPath)
        );
        $createResult = $this->client->certs()->create($createDto);

        $this->assertInstanceOf(CertificateCreateOrUpdateResponseDto::class, $createResult);
        $this->assertTrue($createResult->success);
        $this->assertNotEmpty($createResult->id);

        $this->createdCertId = $createResult->id;

        // READ - Get certificate info
        $certInfo = $this->client->certs()->get($this->createdCertId);

        $this->assertInstanceOf(CertificateInformationResponseDto::class, $certInfo);
        $this->assertEquals($this->createdCertId, $certInfo->id);
        $this->assertEquals($this->testServername, $certInfo->servername);

        // READ - List all certificates
        $listParams = new CertificateListRequestDto();
        $certList = $this->client->certs()->all($listParams);

        $this->assertGreaterThan(0, $certList->total);
        $this->assertNotEmpty($certList->results);

        // Find our created certificate in the list
        $found = false;
        foreach ($certList->results as $cert) {
            if ($cert->id === $this->createdCertId) {
                $found = true;
                $this->assertEquals($this->testServername, $cert->servername);
                break;
            }
        }
        $this->assertTrue($found, 'Created certificate should be in the list');

        // RESOLVE - Resolve certificate by servername
        $resolveResult = $this->client->certs()->resolve($this->testServername);

        $this->assertInstanceOf(CertificateResolveResponseDto::class, $resolveResult);
        $this->assertTrue($resolveResult->success);
        $this->assertEquals($this->createdCertId, $resolveResult->id);

        // DELETE - Delete certificate
        $deleteResult = $this->client->certs()->delete($this->createdCertId);

        $this->assertTrue($deleteResult->success);
        $this->createdCertId = null;
    }

    public function testListCertificatesFiltering(): void
    {
        $createDto = new CreateOrUpdateCertificateRequestDto(
            servername: $this->testServername,
            privateKey: file_get_contents($this->privateKeyPath),
            cert: file_get_contents($this->certPath),
            description: 'Test certificate for filtering'
        );

        $createResult = $this->client->certs()->create($createDto);
        $this->createdCertId = $createResult->id;

        // List with query filter
        $listParams = new CertificateListRequestDto(
            query: $this->testServername
        );
        $certList = $this->client->certs()->all($listParams);

        $this->assertGreaterThanOrEqual(1, $certList->total);

        // Verify our certificate is in the filtered results
        $found = false;
        foreach ($certList->results as $cert) {
            if ($cert->id === $this->createdCertId) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Created certificate should be in filtered list');
    }

    public function testResolveCertificateByServername(): void
    {
        $createDto = new CreateOrUpdateCertificateRequestDto(
            servername: $this->testServername,
            privateKey: file_get_contents($this->privateKeyPath),
            cert: file_get_contents($this->certPath),
            description: 'Test certificate for resolution'
        );

        $createResult = $this->client->certs()->create($createDto);
        $this->createdCertId = $createResult->id;

        // Resolve by servername
        $resolveResult = $this->client->certs()->resolve($this->testServername);

        $this->assertInstanceOf(CertificateResolveResponseDto::class, $resolveResult);
        $this->assertTrue($resolveResult->success);
        $this->assertEquals($this->createdCertId, $resolveResult->id);
    }

    public function testDeleteNonExistentCertificate(): void
    {
        // Try to delete a non-existent certificate
        $fakeId = 'nonexistent' . uniqid();

        $this->expectException(\Exception::class);
        $this->client->certs()->delete($fakeId);
    }

    public function testCreateCertificateWithCA(): void
    {

        // Sample CA chain (for testing purposes only)
        $ca = [
            '-----BEGIN CERTIFICATE-----
MIIDXTCCAkWgAwIBAgIJAKL0UG+mRkSvMA0GCSqGSIb3DQEBCwUAMEUxCzAJBgNV
-----END CERTIFICATE-----',
            '-----BEGIN CERTIFICATE-----
MIIDXTCCAkWgAwIBAgIJAKL0UG+mRkSvMA0GCSqGSIb3DQEBCwUAMEUxCzAJBgNV
-----END CERTIFICATE-----'
        ];

        // CREATE - Create certificate with CA chain
        $createDto = new CreateOrUpdateCertificateRequestDto(
            servername: $this->testServername,
            privateKey: file_get_contents($this->privateKeyPath),
            cert: file_get_contents($this->certPath),
            ca: $ca,
            description: 'Test certificate with CA chain'
        );

        $createResult = $this->client->certs()->create($createDto);

        $this->assertInstanceOf(CertificateCreateOrUpdateResponseDto::class, $createResult);
        $this->assertTrue($createResult->success);
        $this->assertNotEmpty($createResult->id);

        $this->createdCertId = $createResult->id;

        // Verify the certificate was created
        $certInfo = $this->client->certs()->get($this->createdCertId);
        $this->assertEquals($this->testServername, $certInfo->servername);
    }
}
