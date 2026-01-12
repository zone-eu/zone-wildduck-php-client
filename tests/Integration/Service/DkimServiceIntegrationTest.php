<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\Dkim\CreateOrUpdateDkimRequestDto;
use Zone\Wildduck\Dto\Dkim\DkimResponseDto;
use Zone\Wildduck\Dto\Dkim\DkimResolveResponseDto;
use Zone\Wildduck\Dto\Dkim\ListAllDkimRequestDto;

class DkimServiceIntegrationTest extends IntegrationTestCase
{
    private ?string $createdDkimId = null;
    private string $testDomain = 'updates.zone.test';
    private string $privateKeyPath = __DIR__ . '/../Fixtures/privkey.pem';

    protected function tearDown(): void
    {
        if ($this->createdDkimId !== null) {
            try {
                $this->client->dkim()->delete($this->createdDkimId);
            } catch (\Exception $e) {
                // Ignore cleanup errors
            }
            $this->createdDkimId = null;
        }

        parent::tearDown();
    }

    public function testDkimLifecycle(): void
    {
        // Generate unique domain for testing
        $selector = 'default';

        // CREATE - Create DKIM key
        $createDto = new CreateOrUpdateDkimRequestDto(
            domain: $this->testDomain,
            selector: $selector,
            description: 'Test DKIM key for integration testing'
        );

        $createResult = $this->client->dkim()->create($createDto);

        $this->assertInstanceOf(DkimResponseDto::class, $createResult);
        $this->assertTrue($createResult->success);
        $this->assertNotEmpty($createResult->id);

        $this->createdDkimId = $createResult->id;

        // READ - Get DKIM key info
        $dkimInfo = $this->client->dkim()->get($this->createdDkimId);

        $this->assertInstanceOf(DkimResponseDto::class, $dkimInfo);
        $this->assertEquals($this->createdDkimId, $dkimInfo->id);
        $this->assertEquals($this->testDomain, $dkimInfo->domain);
        $this->assertEquals($selector, $dkimInfo->selector);
        $this->assertNotEmpty($dkimInfo->publicKey);

        // READ - List all DKIM keys
        $listParams = new ListAllDkimRequestDto(query: null);
        $dkimList = $this->client->dkim()->all($listParams);

        $this->assertGreaterThan(0, $dkimList->total);
        $this->assertNotEmpty($dkimList->results);

        // Find our created DKIM key in the list
        $found = false;
        foreach ($dkimList->results as $dkim) {
            if ($dkim->id === $this->createdDkimId) {
                $found = true;
                $this->assertEquals($this->testDomain, $dkim->domain);
                break;
            }
        }
        $this->assertTrue($found, 'Created DKIM key should be in the list');

        // RESOLVE - Resolve DKIM by domain
        $resolveResult = $this->client->dkim()->resolve($this->testDomain);

        $this->assertInstanceOf(DkimResolveResponseDto::class, $resolveResult);
        $this->assertTrue($resolveResult->success);
        $this->assertEquals($this->createdDkimId, $resolveResult->id);

        // DELETE - Delete DKIM key
        $deleteResult = $this->client->dkim()->delete($this->createdDkimId);

        $this->assertTrue($deleteResult->success);
        $this->createdDkimId = null;
    }

    public function testCreateDkimWithPrivateKey(): void
    {
        // Generate unique domain for testing
        $selector = 'custom';

        // Generate a simple test private key (this is just for testing, not a real RSA key)
        $privateKey = file_get_contents($this->privateKeyPath);

        // CREATE - Create DKIM key with custom private key
        $createDto = new CreateOrUpdateDkimRequestDto(
            domain: $this->testDomain,
            selector: $selector,
            privateKey: $privateKey,
            description: 'Test DKIM key with custom private key'
        );

        $createResult = $this->client->dkim()->create($createDto);

        $this->assertInstanceOf(DkimResponseDto::class, $createResult);
        $this->assertTrue($createResult->success);
        $this->assertNotEmpty($createResult->id);

        $this->createdDkimId = $createResult->id;

        // Verify the DKIM key was created
        $dkimInfo = $this->client->dkim()->get($this->createdDkimId);
        $this->assertEquals($selector, $dkimInfo->selector);
        $this->assertEquals($this->testDomain, $dkimInfo->domain);
    }

    public function testListDkimKeysFiltering(): void
    {
        // Create a test DKIM key

        $createDto = new CreateOrUpdateDkimRequestDto(
            domain: $this->testDomain,
            selector: 'default',
            description: 'Test DKIM for filtering'
        );

        $createResult = $this->client->dkim()->create($createDto);
        $this->createdDkimId = $createResult->id;

        // List with query filter
        $listParams = new ListAllDkimRequestDto(
            query: $this->testDomain
        );
        $dkimList = $this->client->dkim()->all($listParams);

        $this->assertGreaterThanOrEqual(1, $dkimList->total);

        // Verify our DKIM is in the filtered results
        $found = false;
        foreach ($dkimList->results as $dkim) {
            if ($dkim->id === $this->createdDkimId) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Created DKIM key should be in filtered list');
    }

    public function testResolveDkimByDomain(): void
    {
        // Create a test DKIM key
        $selector = 'mail';

        $createDto = new CreateOrUpdateDkimRequestDto(
            domain: $this->testDomain,
            selector: $selector,
            description: 'Test DKIM for resolution'
        );

        $createResult = $this->client->dkim()->create($createDto);
        $this->createdDkimId = $createResult->id;

        // Resolve by domain
        $resolveResult = $this->client->dkim()->resolve($this->testDomain);

        $this->assertInstanceOf(DkimResolveResponseDto::class, $resolveResult);
        $this->assertTrue($resolveResult->success);
        $this->assertEquals($this->createdDkimId, $resolveResult->id);
    }

    public function testDeleteNonExistentDkim(): void
    {
        // Try to delete a non-existent DKIM key
        $fakeId = 'nonexistent' . uniqid();

        $this->expectException(\Exception::class);
        $this->client->dkim()->delete($fakeId);
    }
}
