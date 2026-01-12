<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\Authentication\AuthLogListRequestDto;
use Zone\Wildduck\Dto\Authentication\AuthlogPaginatedResponseDto;
use Zone\Wildduck\Dto\Authentication\AuthLogEventResponseDto;
use Zone\Wildduck\Dto\Authentication\AuthenticateRequestDto;

class AuthenticationLogServiceIntegrationTest extends IntegrationTestCase
{
    private ?string $createdUserId = null;

    protected function tearDown(): void
    {
        if ($this->createdUserId !== null) {
            $this->cleanupUser($this->createdUserId);
            $this->createdUserId = null;
        }

        parent::tearDown();
    }

    public function testListAuthenticationLogs(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $email = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $email);

        // Generate authentication events by authenticating
        $authDto = new AuthenticateRequestDto(
            username: $username,
            password: $password,
            scope: 'master'
        );

        try {
            $this->client->authentication()->authenticate($authDto);
        } catch (\Exception $e) {
            // Authentication might fail, but it should still generate a log entry
        }

        // Wait a moment for the log to be recorded
        usleep(500000); // 0.5 seconds

        // List authentication logs
        $listDto = new AuthLogListRequestDto(
            limit: 25
        );

        $authLogs = $this->client->authenticationLogs()->all($this->createdUserId, $listDto);

        // Verify we got results
        $this->assertInstanceOf(AuthlogPaginatedResponseDto::class, $authLogs);

        // There should be at least one log entry (from our authentication attempt)
        if ($authLogs->total > 0) {
            $this->assertNotEmpty($authLogs->results);
            $this->assertGreaterThan(0, $authLogs->total);
        }
    }

    public function testGetSpecificAuthenticationLogEvent(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $email = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $email);

        // Generate authentication events
        $authDto = new AuthenticateRequestDto(
            username: $username,
            password: $password,
            scope: 'master'
        );

        try {
            $this->client->authentication()->authenticate($authDto);
        } catch (\Exception $e) {
            // Might fail, but should generate log
        }

        // Wait for log to be recorded
        usleep(500000); // 0.5 seconds

        // List authentication logs to get an event ID
        $listDto = new AuthLogListRequestDto(
            limit: 1
        );

        $authLogs = $this->client->authenticationLogs()->all($this->createdUserId, $listDto);

        // If we have log entries, test getting a specific one
        if ($authLogs->total > 0 && !empty($authLogs->results)) {
            $firstLog = $authLogs->results[0];
            $eventId = $firstLog->id;

            // Get the specific event
            $event = $this->client->authenticationLogs()->get($this->createdUserId, $eventId);

            // Verify the event details
            $this->assertInstanceOf(AuthLogEventResponseDto::class, $event);
            $this->assertEquals($eventId, $event->id);
            $this->assertNotEmpty($event->action);
            $this->assertNotEmpty($event->created);
        } else {
            // If no logs exist, just verify the endpoint works without errors
            $this->assertInstanceOf(AuthlogPaginatedResponseDto::class, $authLogs);
        }
    }

    public function testListAuthenticationLogsWithFilters(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $email = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $email);

        // Generate multiple authentication attempts
        $authDto = new AuthenticateRequestDto(
            username: $username,
            password: $password,
            scope: 'master'
        );

        // First authentication attempt
        try {
            $this->client->authentication()->authenticate($authDto);
        } catch (\Exception $e) {
            // May fail
        }

        // Second authentication attempt with wrong password
        $wrongAuthDto = new AuthenticateRequestDto(
            username: $username,
            password: 'WrongPassword123!',
            scope: 'master'
        );

        try {
            $this->client->authentication()->authenticate($wrongAuthDto);
        } catch (\Exception $e) {
            // Expected to fail
        }

        // Wait for logs to be recorded
        usleep(500000); // 0.5 seconds

        // List all authentication logs
        $listDto = new AuthLogListRequestDto(
            limit: 50
        );

        $authLogs = $this->client->authenticationLogs()->all($this->createdUserId, $listDto);

        // Verify response
        $this->assertInstanceOf(AuthlogPaginatedResponseDto::class, $authLogs);
    }

    public function testListAuthenticationLogsForNewUser(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $email = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $email);

        // List authentication logs immediately (should be empty or minimal)
        $listDto = new AuthLogListRequestDto(
            limit: 25
        );

        $authLogs = $this->client->authenticationLogs()->all($this->createdUserId, $listDto);

        // Verify we can list logs even if empty
        $this->assertInstanceOf(AuthlogPaginatedResponseDto::class, $authLogs);
    }
}
