<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\Filter\CreateFilterRequestDto;
use Zone\Wildduck\Dto\Filter\UpdateFilterRequestDto;
use Zone\Wildduck\Dto\Shared\FilterActionRequestDto;
use Zone\Wildduck\Dto\Shared\FilterQueryRequestDto;
use Zone\Wildduck\Dto\User\CreateUserRequestDto;
use Zone\Wildduck\Dto\User\CreatedUserResponseDto;

class FilterServiceIntegrationTest extends IntegrationTestCase
{
    private ?string $createdUserId = null;
    private ?string $createdFilterId = null;

    protected function tearDown(): void
    {
        if ($this->createdUserId !== null) {
            $this->cleanupUser($this->createdUserId);
            $this->createdUserId = null;
        }

        parent::tearDown();
    }

    public function testFilterLifecycle(): void
    {
        // Create a user first
        $username = $this->generateUniqueUsername();

        $createUserDto = new CreateUserRequestDto(
            username: $username,
            password: 'TestPassword123!',
            address: $this->generateUniqueEmail()
        );

        $userResult = $this->client->users()->create($createUserDto);
        $this->createdUserId = $userResult->id;

        // Create filter
        $createFilterDto = new CreateFilterRequestDto(
            name: 'Spam Filter',
            query: new FilterQueryRequestDto(
                from: 'spam@example.com'
            ),
            action: new FilterActionRequestDto(
                delete: true
            )
        );

        $createResult = $this->client->filters()->create($this->createdUserId, $createFilterDto);
        $this->assertTrue($createResult->success);
        // Note: SuccessResponseDto doesn't include id, we'll get it by listing filters

        // Get the created filter ID by listing filters
        $filters = $this->client->filters()->userAll($this->createdUserId, null);
        $this->assertGreaterThan(0, count($filters->results));
        $this->createdFilterId = $filters->results[0]->id;

        // List filters
        $filters = $this->client->filters()->userAll($this->createdUserId, null);
        $this->assertCount(1, $filters->results);

        // Get specific filter
        $filter = $this->client->filters()->get($this->createdUserId, $this->createdFilterId);
        $this->assertEquals($this->createdFilterId, $filter->id);
        $this->assertEquals('Spam Filter', $filter->name);
        $this->assertFalse($filter->disabled);

        // Update filter
        $updateDto = new UpdateFilterRequestDto(
            name: 'Updated Spam Filter',
            disabled: true
        );

        $updateResult = $this->client->filters()->update($this->createdUserId, $this->createdFilterId, $updateDto);
        $this->assertTrue($updateResult->success);

        // Verify update
        $updatedFilter = $this->client->filters()->get($this->createdUserId, $this->createdFilterId);
        $this->assertEquals('Updated Spam Filter', $updatedFilter->name);
        $this->assertTrue($updatedFilter->disabled);

        // Delete filter
        $deleteResult = $this->client->filters()->delete($this->createdUserId, $this->createdFilterId);
        $this->assertTrue($deleteResult->success);

        // Verify deletion
        $filters = $this->client->filters()->userAll($this->createdUserId, null);
        $this->assertCount(0, $filters->results);
    }

    public function testGlobalFilterList(): void
    {
        // Create a user and filter first
        $username = $this->generateUniqueUsername();

        $createUserDto = new CreateUserRequestDto(
            username: $username,
            password: 'TestPassword123!',
            address: $this->generateUniqueEmail()
        );

        $userResult = $this->client->users()->create($createUserDto);
        $this->createdUserId = $userResult->id;

        // Create a filter for the user
        $createFilterDto = new CreateFilterRequestDto(
            name: 'Test Filter',
            query: new FilterQueryRequestDto(
                from: 'test@example.com'
            ),
            action: new FilterActionRequestDto(
                delete: true
            )
        );

        $createResult = $this->client->filters()->create($this->createdUserId, $createFilterDto);
        $this->assertTrue($createResult->success);
        // Note: SuccessResponseDto doesn't include id
        $this->createdFilterId = null;

        // List all global filters (not user-specific)
        $listDto = new \Zone\Wildduck\Dto\Filter\ListAllFiltersRequestDto(
            forward: null,
            metaData: null,
            limit: 10
        );

        $globalFilters = $this->client->filters()->all($listDto);

        $this->assertGreaterThanOrEqual(0, $globalFilters->total);

        // If there are filters, verify they are ListAllFiltersResponseDto instances
        if (count($globalFilters->results) > 0) {
            $this->assertInstanceOf(\Zone\Wildduck\Dto\Filter\ListAllFiltersResponseDto::class, $globalFilters->results[0]);
        }
    }
}
