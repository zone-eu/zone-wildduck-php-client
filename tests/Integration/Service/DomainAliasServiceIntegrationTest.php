<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\DomainAlias\CreateDomainAliasRequestDto;
use Zone\Wildduck\Dto\DomainAlias\DomainAliasSuccessResponseDto;
use Zone\Wildduck\Dto\DomainAlias\DomainAliasResponseDto;
use Zone\Wildduck\Dto\DomainAlias\ListAllDomainAliasesRequestDto;

class DomainAliasServiceIntegrationTest extends IntegrationTestCase
{
    private ?string $createdAliasId = null;
    private ?string $testAlias = null;

    protected function tearDown(): void
    {
        if ($this->createdAliasId !== null) {
            try {
                $this->client->domainAliases()->delete($this->createdAliasId);
            } catch (\Exception $e) {
                // Ignore cleanup errors
            }
            $this->createdAliasId = null;
        }

        parent::tearDown();
    }

    public function testDomainAliasLifecycle(): void
    {
        // Generate unique domains for testing
        $this->testAlias = 'alias-' . uniqid() . '-' . time() . '.example.com';
        $targetDomain = 'target-' . uniqid() . '-' . time() . '.example.com';

        // CREATE - Create domain alias
        $createDto = new CreateDomainAliasRequestDto(
            alias: $this->testAlias,
            domain: $targetDomain
        );

        $createResult = $this->client->domainAliases()->create($createDto);

        $this->assertInstanceOf(DomainAliasSuccessResponseDto::class, $createResult);
        $this->assertTrue($createResult->success);
        $this->assertNotEmpty($createResult->id);

        $this->createdAliasId = $createResult->id;

        // READ - Get domain alias info
        $aliasInfo = $this->client->domainAliases()->get($this->createdAliasId);

        $this->assertInstanceOf(DomainAliasResponseDto::class, $aliasInfo);
        $this->assertEquals($this->createdAliasId, $aliasInfo->id);
        $this->assertEquals($this->testAlias, $aliasInfo->alias);
        $this->assertEquals($targetDomain, $aliasInfo->domain);

        // READ - List all domain aliases
        $listParams = new ListAllDomainAliasesRequestDto();
        $aliasList = $this->client->domainAliases()->all($listParams);

        $this->assertGreaterThan(0, $aliasList->total);
        $this->assertNotEmpty($aliasList->results);

        // Find our created alias in the list
        $found = false;
        foreach ($aliasList->results as $alias) {
            if ($alias->id === $this->createdAliasId) {
                $found = true;
                $this->assertEquals($this->testAlias, $alias->alias);
                $this->assertEquals($targetDomain, $alias->domain);
                break;
            }
        }
        $this->assertTrue($found, 'Created domain alias should be in the list');

        // RESOLVE - Resolve domain alias
        $resolveResult = $this->client->domainAliases()->resolve($this->testAlias);

        $this->assertInstanceOf(DomainAliasSuccessResponseDto::class, $resolveResult);
        $this->assertTrue($resolveResult->success);
        $this->assertEquals($this->createdAliasId, $resolveResult->id);

        // DELETE - Delete domain alias
        $deleteResult = $this->client->domainAliases()->delete($this->createdAliasId);

        $this->assertTrue($deleteResult->success);
        $this->createdAliasId = null;
    }

    public function testCreateMultipleDomainAliases(): void
    {
        // Create multiple aliases for the same target domain
        $targetDomain = 'target-multi-' . uniqid() . '-' . time() . '.example.com';
        $alias1 = 'alias1-' . uniqid() . '-' . time() . '.example.com';
        $alias2 = 'alias2-' . uniqid() . '-' . time() . '.example.com';

        // Create first alias
        $createDto1 = new CreateDomainAliasRequestDto(
            alias: $alias1,
            domain: $targetDomain
        );

        $createResult1 = $this->client->domainAliases()->create($createDto1);
        $this->assertTrue($createResult1->success);
        $this->createdAliasId = $createResult1->id;

        // Create second alias
        $createDto2 = new CreateDomainAliasRequestDto(
            alias: $alias2,
            domain: $targetDomain
        );

        $createResult2 = $this->client->domainAliases()->create($createDto2);
        $this->assertTrue($createResult2->success);

        // Verify both aliases exist
        $aliasInfo1 = $this->client->domainAliases()->get($createResult1->id);
        $this->assertEquals($targetDomain, $aliasInfo1->domain);

        $aliasInfo2 = $this->client->domainAliases()->get($createResult2->id);
        $this->assertEquals($targetDomain, $aliasInfo2->domain);

        // Cleanup second alias
        $this->client->domainAliases()->delete($createResult2->id);

        // First alias will be cleaned up in tearDown
    }

    public function testListDomainAliasesFiltering(): void
    {
        // Create a test domain alias
        $this->testAlias = 'alias-filter-' . uniqid() . '-' . time() . '.example.com';
        $targetDomain = 'target-filter-' . uniqid() . '-' . time() . '.example.com';

        $createDto = new CreateDomainAliasRequestDto(
            alias: $this->testAlias,
            domain: $targetDomain
        );

        $createResult = $this->client->domainAliases()->create($createDto);
        $this->createdAliasId = $createResult->id;

        // List with query filter
        $listParams = new ListAllDomainAliasesRequestDto(
            query: $this->testAlias
        );
        $aliasList = $this->client->domainAliases()->all($listParams);

        $this->assertGreaterThanOrEqual(1, $aliasList->total);

        // Verify our alias is in the filtered results
        $found = false;
        foreach ($aliasList->results as $alias) {
            if ($alias->id === $this->createdAliasId) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Created domain alias should be in filtered list');
    }

    public function testResolveDomainAlias(): void
    {
        // Create a test domain alias
        $this->testAlias = 'alias-resolve-' . uniqid() . '-' . time() . '.example.com';
        $targetDomain = 'target-resolve-' . uniqid() . '-' . time() . '.example.com';

        $createDto = new CreateDomainAliasRequestDto(
            alias: $this->testAlias,
            domain: $targetDomain
        );

        $createResult = $this->client->domainAliases()->create($createDto);
        $this->createdAliasId = $createResult->id;

        // Resolve the alias
        $resolveResult = $this->client->domainAliases()->resolve($this->testAlias);

        $this->assertInstanceOf(DomainAliasSuccessResponseDto::class, $resolveResult);
        $this->assertTrue($resolveResult->success);
        $this->assertEquals($this->createdAliasId, $resolveResult->id);
    }

    public function testDeleteNonExistentDomainAlias(): void
    {
        // Try to delete a non-existent domain alias
        $fakeId = 'nonexistent' . uniqid();

        $this->expectException(\Exception::class);
        $this->client->domainAliases()->delete($fakeId);
    }
}
