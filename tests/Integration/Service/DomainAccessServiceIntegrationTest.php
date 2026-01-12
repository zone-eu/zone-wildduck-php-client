<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\DomainAccess\CreateAllowedDomainRequestDto;
use Zone\Wildduck\Dto\DomainAccess\CreateBlockedDomainRequestDto;
use Zone\Wildduck\Dto\DomainAccess\DomainAccessSuccessResponseDto;

class DomainAccessServiceIntegrationTest extends IntegrationTestCase
{
    private array $createdDomains = [];

    protected function tearDown(): void
    {
        // Cleanup all created domains
        foreach ($this->createdDomains as $domain) {
            try {
                $this->client->domainAccess()->deleteDomainListing($domain);
            } catch (\Exception $e) {
                // Ignore cleanup errors
            }
        }
        $this->createdDomains = [];

        parent::tearDown();
    }

    public function testDomainAccessAllowlistLifecycle(): void
    {
        $this->markTestSkipped('Domain access query and delete functionality not available on test server');
    }

    public function testDomainAccessBlocklistLifecycle(): void
    {
        $this->markTestSkipped('Domain access query and delete functionality not available on test server');
    }

    public function testCreateMultipleDomainsForSameTag(): void
    {
        $this->markTestSkipped('Domain access query and delete functionality not available on test server');
    }

    public function testMixedAllowAndBlockLists(): void
    {
        $this->markTestSkipped('Domain access query and delete functionality not available on test server');
    }

    public function testDeleteDomainRemovesFromAllLists(): void
    {
        $this->markTestSkipped('Domain access query and delete functionality not available on test server');
    }

    public function testCreateAllowedDomain(): void
    {
        // Test that we can successfully create an allowed domain
        $testTag = 'test-tag-create-' . uniqid() . '-' . time();
        $testDomain = 'create-allowed-' . uniqid() . '-' . time() . '.example.com';

        $createDto = new CreateAllowedDomainRequestDto(domain: $testDomain);
        $result = $this->client->domainAccess()->createAllowedDomain($testTag, $createDto);

        $this->assertInstanceOf(DomainAccessSuccessResponseDto::class, $result);
        $this->assertTrue($result->success);
        $this->assertNotEmpty($result->id);
    }

    public function testCreateBlockedDomain(): void
    {
        // Test that we can successfully create a blocked domain
        $testTag = 'test-tag-create-block-' . uniqid() . '-' . time();
        $testDomain = 'create-blocked-' . uniqid() . '-' . time() . '.example.com';

        $createDto = new CreateBlockedDomainRequestDto(domain: $testDomain);
        $result = $this->client->domainAccess()->createBlockedDomain($testTag, $createDto);

        $this->assertInstanceOf(DomainAccessSuccessResponseDto::class, $result);
        $this->assertTrue($result->success);
        $this->assertNotEmpty($result->id);
    }
}
