<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\Mailbox\CreateMailboxRequestDto;
use Zone\Wildduck\Dto\Mailbox\UpdateMailboxRequestDto;
use Zone\Wildduck\Dto\User\CreateUserRequestDto;
use Zone\Wildduck\Dto\User\CreatedUserResponseDto;

class MailboxServiceIntegrationTest extends IntegrationTestCase
{
    private ?string $createdUserId = null;
    private ?string $createdMailboxId = null;

    protected function tearDown(): void
    {
        if ($this->createdUserId !== null) {
            $this->cleanupUser($this->createdUserId);
            $this->createdUserId = null;
        }

        parent::tearDown();
    }

    public function testMailboxLifecycle(): void
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

        // List default mailboxes
        $mailboxes = $this->client->mailboxes()->all($this->createdUserId);
        $initialCount = $mailboxes->total;
        $this->assertGreaterThan(0, $initialCount); // Should have default mailboxes

        // Create custom mailbox
        $createMailboxDto = new CreateMailboxRequestDto(
            path: 'Custom Folder',
            retention: 30
        );

        $mailbox = $this->client->mailboxes()->create($this->createdUserId, $createMailboxDto);
        $this->assertNotEmpty($mailbox->id);
        $this->createdMailboxId = $mailbox->id;

        // Verify mailbox was created
        $mailboxes = $this->client->mailboxes()->all($this->createdUserId);
        $this->assertEquals($initialCount + 1, $mailboxes->total);

        // Get specific mailbox
        $fetchedMailbox = $this->client->mailboxes()->get($this->createdUserId, $this->createdMailboxId);
        $this->assertEquals($this->createdMailboxId, $fetchedMailbox->id);
        $this->assertEquals('Custom Folder', $fetchedMailbox->name);

        // Update mailbox
        $updateDto = new UpdateMailboxRequestDto(
            retention: 60
        );

        $updateResult = $this->client->mailboxes()->update($this->createdUserId, $this->createdMailboxId, $updateDto);
        $this->assertTrue($updateResult->success);

        // Verify update
        $updatedMailbox = $this->client->mailboxes()->get($this->createdUserId, $this->createdMailboxId);

        // Delete mailbox
        $deleteResult = $this->client->mailboxes()->delete($this->createdUserId, $this->createdMailboxId);
        $this->assertTrue($deleteResult->success);

        // Verify deletion
        $mailboxes = $this->client->mailboxes()->all($this->createdUserId);
        $this->assertEquals($initialCount, $mailboxes->total);
    }

    public function testDeleteAllMessages(): void
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

        // Create a custom mailbox
        $createMailboxDto = new CreateMailboxRequestDto(
            path: 'TestMessages',
            retention: 30
        );

        $mailbox = $this->client->mailboxes()->create($this->createdUserId, $createMailboxDto);
        $this->createdMailboxId = $mailbox->id;

        // Upload test messages to the mailbox
        $rawMessage1 = "From: sender1@example.com\r\n"
            . "To: recipient@example.com\r\n"
            . "Subject: Test Message 1\r\n"
            . "Date: Thu, 9 Jan 2026 10:00:00 +0000\r\n"
            . "\r\n"
            . "Test message body 1.";

        $rawMessage2 = "From: sender2@example.com\r\n"
            . "To: recipient@example.com\r\n"
            . "Subject: Test Message 2\r\n"
            . "Date: Thu, 9 Jan 2026 11:00:00 +0000\r\n"
            . "\r\n"
            . "Test message body 2.";

        $messageId1 = $this->uploadTestMessage($this->createdUserId, $this->createdMailboxId, $rawMessage1);
        $messageId2 = $this->uploadTestMessage($this->createdUserId, $this->createdMailboxId, $rawMessage2);

        $this->assertNotEmpty($messageId1);
        $this->assertNotEmpty($messageId2);

        // Get message count before deletion
        $mailboxBefore = $this->client->mailboxes()->get($this->createdUserId, $this->createdMailboxId);
        $this->assertGreaterThanOrEqual(2, $mailboxBefore->total);

        // Delete all messages from the mailbox
        $deleteDto = new \Zone\Wildduck\Dto\Mailbox\DeleteAllMessagesRequestDto();

        $deleteResult = $this->client->mailboxes()->deleteAllMessages(
            $this->createdUserId,
            $this->createdMailboxId,
            $deleteDto
        );

        $this->assertInstanceOf(\Zone\Wildduck\Dto\Mailbox\DeleteAllMessagesResponseDto::class, $deleteResult);
        $this->assertTrue($deleteResult->success);
        $this->assertGreaterThanOrEqual(2, $deleteResult->deleted);

        // Verify messages were deleted
        $mailboxAfter = $this->client->mailboxes()->get($this->createdUserId, $this->createdMailboxId);
        $this->assertLessThan($mailboxBefore->total, $mailboxAfter->total);
    }
}
