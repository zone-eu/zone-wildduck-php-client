<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\ApplicationPassword\CreateApplicationPasswordRequestDto;
use Zone\Wildduck\Dto\ApplicationPassword\CreateApplicationPasswordResponseDto;
use Zone\Wildduck\Dto\ApplicationPassword\ApplicationPasswordResponseDto;
use Zone\Wildduck\Dto\ApplicationPassword\ListAllUserApplicationPasswordRequestDto;
use Zone\Wildduck\Dto\ApplicationPassword\DeleteApplicationPasswordRequestDto;
use Zone\Wildduck\Dto\PaginatedResultDto;

class ApplicationPasswordServiceIntegrationTest extends IntegrationTestCase
{
    private ?string $createdUserId = null;
    private ?string $createdAspId = null;

    protected function tearDown(): void
    {
        // Application passwords are automatically deleted when the user is deleted
        if ($this->createdUserId !== null) {
            $this->cleanupUser($this->createdUserId);
            $this->createdUserId = null;
        }

        parent::tearDown();
    }

    public function testApplicationPasswordLifecycle(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $address = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $address);

        // Create an application password
        // Note: Not providing a custom password - let API generate it
        $createDto = new CreateApplicationPasswordRequestDto(
            description: 'Test App Password',
            scopes: ['imap', 'smtp']
        );

        $createResult = $this->client->applicationPasswords()->create($this->createdUserId, $createDto);

        $this->assertInstanceOf(CreateApplicationPasswordResponseDto::class, $createResult);
        $this->assertNotEmpty($createResult->id);
        $this->assertNotEmpty($createResult->password);

        $this->createdAspId = $createResult->id;

        // Get the specific application password
        $asp = $this->client->applicationPasswords()->get($this->createdUserId, $this->createdAspId);

        $this->assertInstanceOf(ApplicationPasswordResponseDto::class, $asp);
        $this->assertEquals($this->createdAspId, $asp->id);
        $this->assertEquals('Test App Password', $asp->description);
        $this->assertContains('imap', $asp->scopes);
        $this->assertContains('smtp', $asp->scopes);

        // List all application passwords
        $listDto = new ListAllUserApplicationPasswordRequestDto(showAll: false);
        $allAsps = $this->client->applicationPasswords()->all($this->createdUserId, $listDto);

        $this->assertInstanceOf(PaginatedResultDto::class, $allAsps);
        $this->assertGreaterThan(0, count($allAsps->results));

        // Find our created app password in the list
        $found = false;
        foreach ($allAsps->results as $result) {
            $this->assertInstanceOf(ApplicationPasswordResponseDto::class, $result);
            if ($result->id === $this->createdAspId) {
                $found = true;
                $this->assertEquals('Test App Password', $result->description);
                break;
            }
        }
        $this->assertTrue($found, 'Created application password should be in the list');

        // Delete the application password
        $deleteDto = new DeleteApplicationPasswordRequestDto();
        $deleteResult = $this->client->applicationPasswords()->delete(
            $this->createdUserId,
            $this->createdAspId,
            $deleteDto
        );

        $this->assertTrue($deleteResult->success);

        // Verify deletion - listing should not include the deleted password
        $allAspsAfterDelete = $this->client->applicationPasswords()->all($this->createdUserId, new ListAllUserApplicationPasswordRequestDto(showAll: false));

        $foundAfterDelete = false;
        foreach ($allAspsAfterDelete->results as $result) {
            if ($result->id === $this->createdAspId) {
                $foundAfterDelete = true;
                break;
            }
        }
        $this->assertFalse($foundAfterDelete, 'Deleted application password should not be in the list');

        $this->createdAspId = null; // Already deleted
    }

    public function testCreateApplicationPasswordWithDifferentScopes(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $address = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $address);

        // Create app password with IMAP only
        $createDto = new CreateApplicationPasswordRequestDto(
            description: 'IMAP Only Password',
            scopes: ['imap']
        );

        $createResult = $this->client->applicationPasswords()->create($this->createdUserId, $createDto);

        $this->assertNotEmpty($createResult->id);
        $this->assertNotEmpty($createResult->password);

        // Get the app password and verify scopes
        $asp = $this->client->applicationPasswords()->get($this->createdUserId, $createResult->id);

        $this->assertEquals('IMAP Only Password', $asp->description);
        $this->assertContains('imap', $asp->scopes);
        $this->assertCount(1, $asp->scopes);
    }

    public function testCreateApplicationPasswordWithAllScopes(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $address = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $address);

        // Create app password with all scopes
        $createDto = new CreateApplicationPasswordRequestDto(
            description: 'All Scopes Password',
            scopes: ['*']
        );

        $createResult = $this->client->applicationPasswords()->create($this->createdUserId, $createDto);

        $this->assertNotEmpty($createResult->id);
        $this->assertNotEmpty($createResult->password);

        // Get the app password and verify scopes
        $asp = $this->client->applicationPasswords()->get($this->createdUserId, $createResult->id);

        $this->assertEquals('All Scopes Password', $asp->description);
        $this->assertNotEmpty($asp->scopes);
    }

    public function testListApplicationPasswordsForUserWithoutPasswords(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $address = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $address);

        // List application passwords (should be empty)
        $listDto = new ListAllUserApplicationPasswordRequestDto(showAll: false);
        $allAsps = $this->client->applicationPasswords()->all($this->createdUserId, $listDto);

        $this->assertInstanceOf(PaginatedResultDto::class, $allAsps);
        $this->assertCount(0, $allAsps->results);
    }
}
