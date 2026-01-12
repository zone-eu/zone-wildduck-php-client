<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\Autoreply\UpdateAutoreplyRequestDto;
use Zone\Wildduck\Dto\Autoreply\AutoreplyResponseDto;
use Zone\Wildduck\Dto\Autoreply\AutoreplyUpdateResponseDto;
use Zone\Wildduck\Dto\Shared\SuccessResponseDto;

class AutoreplyServiceIntegrationTest extends IntegrationTestCase
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

    public function testAutoreplyLifecycle(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $email = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $email);

        // Initially, get autoreply (should be disabled or not set)
        $initialAutoreply = $this->client->autoreplies()->get($this->createdUserId);
        $this->assertInstanceOf(AutoreplyResponseDto::class, $initialAutoreply);
        $this->assertTrue($initialAutoreply->success);

        // Update autoreply - enable it with subject and message
        $updateDto = new UpdateAutoreplyRequestDto(
            status: true,
            name: 'Test User',
            subject: 'Out of Office',
            text: 'I am currently out of office and will respond when I return.',
            html: '<p>I am currently <strong>out of office</strong> and will respond when I return.</p>',
            start: '2026-01-10T00:00:00.000Z',
            end: '2026-01-20T00:00:00.000Z'
        );

        $updateResult = $this->client->autoreplies()->update($this->createdUserId, $updateDto);
        $this->assertInstanceOf(AutoreplyUpdateResponseDto::class, $updateResult);
        $this->assertTrue($updateResult->success);

        // Get autoreply again to verify it was updated
        $updatedAutoreply = $this->client->autoreplies()->get($this->createdUserId);
        $this->assertInstanceOf(AutoreplyResponseDto::class, $updatedAutoreply);
        $this->assertTrue($updatedAutoreply->success);
        $this->assertTrue($updatedAutoreply->status);
        $this->assertEquals('Out of Office', $updatedAutoreply->subject);
        $this->assertStringContainsString('out of office', $updatedAutoreply->text);

        // Delete/disable autoreply
        $deleteResult = $this->client->autoreplies()->delete($this->createdUserId);
        $this->assertInstanceOf(SuccessResponseDto::class, $deleteResult);
        $this->assertTrue($deleteResult->success);

        // Verify autoreply is disabled
        $finalAutoreply = $this->client->autoreplies()->get($this->createdUserId);
        $this->assertInstanceOf(AutoreplyResponseDto::class, $finalAutoreply);
        $this->assertTrue($finalAutoreply->success);
        $this->assertFalse($finalAutoreply->status);
    }

    public function testUpdateAutoreplyMultipleTimes(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $email = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $email);

        // Enable autoreply with initial message
        $updateDto1 = new UpdateAutoreplyRequestDto(
            status: true,
            subject: 'First Subject',
            text: 'First message'
        );

        $updateResult1 = $this->client->autoreplies()->update($this->createdUserId, $updateDto1);
        $this->assertTrue($updateResult1->success);

        // Update autoreply with different message
        $updateDto2 = new UpdateAutoreplyRequestDto(
            status: true,
            subject: 'Second Subject',
            text: 'Second message'
        );

        $updateResult2 = $this->client->autoreplies()->update($this->createdUserId, $updateDto2);
        $this->assertTrue($updateResult2->success);

        // Verify the latest update
        $autoreply = $this->client->autoreplies()->get($this->createdUserId);
        $this->assertEquals('Second Subject', $autoreply->subject);
        $this->assertEquals('Second message', $autoreply->text);
    }

    public function testDisableAutoreplyByUpdatingStatus(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $email = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $email);

        // Enable autoreply
        $enableDto = new UpdateAutoreplyRequestDto(
            status: true,
            subject: 'Away',
            text: 'I am away'
        );

        $this->client->autoreplies()->update($this->createdUserId, $enableDto);

        // Verify it's enabled
        $autoreply1 = $this->client->autoreplies()->get($this->createdUserId);
        $this->assertTrue($autoreply1->status);

        // Disable by updating status to false
        $disableDto = new UpdateAutoreplyRequestDto(
            status: false
        );

        $this->client->autoreplies()->update($this->createdUserId, $disableDto);

        // Verify it's disabled
        $autoreply2 = $this->client->autoreplies()->get($this->createdUserId);
        $this->assertFalse($autoreply2->status);
    }
}
