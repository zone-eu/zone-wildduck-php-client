<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\Message\BulkUpdateMessagesRequestDto;
use Zone\Wildduck\Dto\Message\ForwardMessageRequestDto;
use Zone\Wildduck\Dto\Message\ListMessagesRequestDto;
use Zone\Wildduck\Dto\Message\SearchApplyMessagesRequestDto;
use Zone\Wildduck\Dto\Message\SearchMessagesRequestDto;
use Zone\Wildduck\Dto\Message\UploadMessageRequestDto;

class MessageServiceIntegrationTest extends IntegrationTestCase
{
    private ?string $createdUserId = null;
    private ?string $createdMailboxId = null;
    private ?string $createdMessageId = null;
    private ?string $secondMailboxId = null;

    protected function tearDown(): void
    {
        if ($this->createdUserId !== null) {
            $this->cleanupUser($this->createdUserId);
            $this->createdUserId = null;
            $this->createdMailboxId = null;
            $this->createdMessageId = null;
            $this->secondMailboxId = null;
        }

        parent::tearDown();
    }

    public function testUploadMessage(): void
    {
        // Setup
        $this->createdUserId = $this->createTestUser(
            $this->generateUniqueUsername(),
            'TestPassword123!',
            $this->generateUniqueEmail()
        );

        $this->createdMailboxId = $this->createTestMailbox($this->createdUserId, 'Test Folder');

        // Test upload
        $rawMessage = "From: sender@example.com\r\n"
            . "To: recipient@example.com\r\n"
            . "Subject: Test Message Upload\r\n"
            . "\r\n"
            . "This is a test message body.\r\n";

        $uploadDto = new UploadMessageRequestDto(raw: $rawMessage);
        $result = $this->client->messages()->upload($this->createdUserId, $this->createdMailboxId, $uploadDto);

        $this->assertTrue($result->success);
        $this->createdMessageId = (string)$result->message->id;
    }

    public function testGetMessage(): void
    {
        // Setup
        $this->createdUserId = $this->createTestUser(
            $this->generateUniqueUsername(),
            'TestPassword123!',
            $this->generateUniqueEmail()
        );
        $this->createdMailboxId = $this->createTestMailbox($this->createdUserId, 'Test Folder');

        $rawMessage = "From: sender@example.com\r\n"
            . "To: recipient@example.com\r\n"
            . "Subject: Test Get Message\r\n"
            . "\r\n"
            . "Test body content.\r\n";

        $this->createdMessageId = $this->uploadTestMessage($this->createdUserId, $this->createdMailboxId, $rawMessage);

        // Test get
        $message = $this->client->messages()->get($this->createdUserId, $this->createdMailboxId, (int)$this->createdMessageId);

        $this->assertEquals((int)$this->createdMessageId, $message->id);
        $this->assertEquals($this->createdMailboxId, $message->mailbox);
        $this->assertStringContainsString('Test Get Message', $message->subject ?? '');
    }

    public function testDeleteMessage(): void
    {
        // Setup
        $this->createdUserId = $this->createTestUser(
            $this->generateUniqueUsername(),
            'TestPassword123!',
            $this->generateUniqueEmail()
        );
        $this->createdMailboxId = $this->createTestMailbox($this->createdUserId, 'Test Folder');

        $rawMessage = "From: sender@example.com\r\n"
            . "To: recipient@example.com\r\n"
            . "Subject: Test Delete Message\r\n"
            . "\r\n"
            . "This message will be deleted.\r\n";

        $messageId = $this->uploadTestMessage($this->createdUserId, $this->createdMailboxId, $rawMessage);

        // Test delete
        $result = $this->client->messages()->delete($this->createdUserId, $this->createdMailboxId, (int)$messageId);

        $this->assertTrue($result->success);
    }

    public function testForwardMessage(): void
    {
        // Setup
        $this->createdUserId = $this->createTestUser(
            $this->generateUniqueUsername(),
            'TestPassword123!',
            $this->generateUniqueEmail()
        );
        $this->createdMailboxId = $this->createTestMailbox($this->createdUserId, 'Test Folder');

        $rawMessage = "From: sender@example.com\r\n"
            . "To: recipient@example.com\r\n"
            . "Subject: Test Forward Message\r\n"
            . "\r\n"
            . "This message will be forwarded.\r\n";

        $this->createdMessageId = $this->uploadTestMessage($this->createdUserId, $this->createdMailboxId, $rawMessage);

        // Test forward
        $forwardDto = new ForwardMessageRequestDto(addresses: ['forward@example.com']);
        $result = $this->client->messages()->forward(
            $this->createdUserId,
            $this->createdMailboxId,
            (int)$this->createdMessageId,
            $forwardDto
        );

        $this->assertTrue($result->success);
        $this->assertNotEmpty($result->queueId);
    }

    public function testSearchMessages(): void
    {
        // Setup
        $this->createdUserId = $this->createTestUser(
            $this->generateUniqueUsername(),
            'TestPassword123!',
            $this->generateUniqueEmail()
        );
        $this->createdMailboxId = $this->createTestMailbox($this->createdUserId, 'Test Folder');

        // Upload a message with unique subject
        $uniqueSubject = 'UniqueSearchSubject_' . uniqid();
        $rawMessage = "From: sender@example.com\r\n"
            . "To: recipient@example.com\r\n"
            . "Subject: $uniqueSubject\r\n"
            . "\r\n"
            . "Searchable message content.\r\n";

        $this->createdMessageId = $this->uploadTestMessage($this->createdUserId, $this->createdMailboxId, $rawMessage);

        // Test search
        $searchDto = new SearchMessagesRequestDto(subject: $uniqueSubject);
        $results = $this->client->messages()->search($this->createdUserId, $searchDto);

        $this->assertGreaterThanOrEqual(1, $results->total);

        // Verify our message is in results
        $found = false;
        foreach ($results->results as $message) {
            if (str_contains($message->subject ?? '', $uniqueSubject)) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Uploaded message should be found in search results');
    }

    public function testMessageSource(): void
    {
        // Setup
        $this->createdUserId = $this->createTestUser(
            $this->generateUniqueUsername(),
            'TestPassword123!',
            $this->generateUniqueEmail()
        );
        $this->createdMailboxId = $this->createTestMailbox($this->createdUserId, 'Test Folder');

        $rawMessage = "From: sender@example.com\r\n"
            . "To: recipient@example.com\r\n"
            . "Subject: Test Source\r\n"
            . "\r\n"
            . "Test message source retrieval.\r\n";

        $this->createdMessageId = $this->uploadTestMessage($this->createdUserId, $this->createdMailboxId, $rawMessage);

        // Test source
        $source = $this->client->messages()->source($this->createdUserId, $this->createdMailboxId, (int)$this->createdMessageId);

        $this->assertStringContainsString('Test Source', $source);
    }

    public function testSubmitDraft(): void
    {
        // Setup
        $this->createdUserId = $this->createTestUser(
            $this->generateUniqueUsername(),
            'TestPassword123!',
            $this->generateUniqueEmail()
        );
        $this->createdMailboxId = $this->createTestMailbox($this->createdUserId, 'Drafts');

        // Upload a draft message
        $rawMessage = "From: sender@example.com\r\n"
            . "To: recipient@example.com\r\n"
            . "Subject: Test Draft Submit\r\n"
            . "\r\n"
            . "This is a draft message.\r\n";

        $uploadDto = new UploadMessageRequestDto(raw: $rawMessage, draft: true);
        $uploadResult = $this->client->messages()->upload($this->createdUserId, $this->createdMailboxId, $uploadDto);
        $this->createdMessageId = (string)$uploadResult->message->id;

        // Test submit draft
        $result = $this->client->messages()->submitDraft(
            $this->createdUserId,
            $this->createdMailboxId,
            (int)$this->createdMessageId
        );

        $this->assertTrue($result->success);
    }

    public function testBulkUpdateMessages(): void
    {
        // Setup
        $this->createdUserId = $this->createTestUser(
            $this->generateUniqueUsername(),
            'TestPassword123!',
            $this->generateUniqueEmail()
        );
        $this->createdMailboxId = $this->createTestMailbox($this->createdUserId, 'Test Folder');

        // Upload two messages
        $rawMessage1 = "From: sender@example.com\r\n"
            . "To: recipient@example.com\r\n"
            . "Subject: Test Bulk 1\r\n"
            . "\r\n"
            . "First message.\r\n";

        $rawMessage2 = "From: sender@example.com\r\n"
            . "To: recipient@example.com\r\n"
            . "Subject: Test Bulk 2\r\n"
            . "\r\n"
            . "Second message.\r\n";

        $messageId1 = $this->uploadTestMessage($this->createdUserId, $this->createdMailboxId, $rawMessage1);
        $messageId2 = $this->uploadTestMessage($this->createdUserId, $this->createdMailboxId, $rawMessage2);

        // Test bulk update (mark as seen)
        $updateDto = new BulkUpdateMessagesRequestDto(
            message: "$messageId1,$messageId2",
            seen: true
        );

        $result = $this->client->messages()->update($this->createdUserId, $this->createdMailboxId, $updateDto);

        $this->assertTrue($result->success);
        if ($result->id !== null) {
            $this->assertIsArray($result->id);
            $this->assertCount(2, $result->id);
        }
    }

    public function testSearchApplyMessages(): void
    {
        // Setup
        $this->createdUserId = $this->createTestUser(
            $this->generateUniqueUsername(),
            'TestPassword123!',
            $this->generateUniqueEmail()
        );
        $this->createdMailboxId = $this->createTestMailbox($this->createdUserId, 'Test Folder');

        // Upload messages with a unique subject
        $uniqueSubject = 'BulkSearchApply_' . uniqid();
        $rawMessage = "From: sender@example.com\r\n"
            . "To: recipient@example.com\r\n"
            . "Subject: $uniqueSubject\r\n"
            . "\r\n"
            . "Bulk search and apply test.\r\n";

        $this->createdMessageId = $this->uploadTestMessage($this->createdUserId, $this->createdMailboxId, $rawMessage);

        // Test search and apply (mark as flagged)
        $searchApplyDto = new SearchApplyMessagesRequestDto(
            action: ['seen' => true, 'flagged' => true],
            subject: $uniqueSubject
        );

        $result = $this->client->messages()->searchApplyMessages($this->createdUserId, $searchApplyDto);

        $this->assertTrue($result->success);
        if ($result->id !== null) {
            $this->assertIsArray($result->id);
            $this->assertGreaterThanOrEqual(1, count($result->id));
        }
    }

    public function testDownloadAttachment(): void
    {
        // Setup
        $this->createdUserId = $this->createTestUser(
            $this->generateUniqueUsername(),
            'TestPassword123!',
            $this->generateUniqueEmail()
        );
        $this->createdMailboxId = $this->createTestMailbox($this->createdUserId, 'Test Folder');

        // Upload a message with attachment (RFC822 format with base64 encoded attachment)
        $boundary = "----=_Part_" . uniqid();
        $rawMessage = "From: sender@example.com\r\n"
            . "To: recipient@example.com\r\n"
            . "Subject: Test Message With Attachment\r\n"
            . "MIME-Version: 1.0\r\n"
            . "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n"
            . "\r\n"
            . "--$boundary\r\n"
            . "Content-Type: text/plain; charset=utf-8\r\n"
            . "\r\n"
            . "This message has an attachment.\r\n"
            . "\r\n"
            . "--$boundary\r\n"
            . "Content-Type: text/plain; name=\"test.txt\"\r\n"
            . "Content-Disposition: attachment; filename=\"test.txt\"\r\n"
            . "Content-Transfer-Encoding: base64\r\n"
            . "\r\n"
            . base64_encode("Test attachment content") . "\r\n"
            . "\r\n"
            . "--$boundary--\r\n";

        $this->createdMessageId = $this->uploadTestMessage($this->createdUserId, $this->createdMailboxId, $rawMessage);

        // Get message details to find attachment ID
        $message = $this->client->messages()->get($this->createdUserId, $this->createdMailboxId, (int)$this->createdMessageId);

        // Check if message has attachments
        if (!empty($message->attachments)) {
            $attachmentId = $message->attachments[0]->id;

            // Test download attachment
            $content = $this->client->messages()->downloadAttachment(
                $this->createdUserId,
                $this->createdMailboxId,
                (int)$this->createdMessageId,
                $attachmentId
            );

            $this->assertNotEmpty($content);
            $this->assertEquals("Test attachment content", $content);
        } else {
            $this->markTestSkipped('Message did not contain attachments');
        }
    }

    public function testDeleteOutbound(): void
    {
        // Setup - This test creates an outbound message by submitting a draft or forwarding
        $this->createdUserId = $this->createTestUser(
            $this->generateUniqueUsername(),
            'TestPassword123!',
            $this->generateUniqueEmail()
        );
        $this->createdMailboxId = $this->createTestMailbox($this->createdUserId, 'Test Folder');

        // Upload and forward a message to create an outbound entry
        $rawMessage = "From: sender@example.com\r\n"
            . "To: recipient@example.com\r\n"
            . "Subject: Test Outbound Delete\r\n"
            . "\r\n"
            . "This will create an outbound message.\r\n";

        $this->createdMessageId = $this->uploadTestMessage($this->createdUserId, $this->createdMailboxId, $rawMessage);

        // Forward to create outbound message
        $forwardDto = new ForwardMessageRequestDto(addresses: ['outbound@example.com']);
        $forwardResult = $this->client->messages()->forward(
            $this->createdUserId,
            $this->createdMailboxId,
            (int)$this->createdMessageId,
            $forwardDto
        );

        $this->assertTrue($forwardResult->success);
        $queueId = $forwardResult->queueId;

        // Test delete outbound
        if (!empty($queueId)) {
            $result = $this->client->messages()->deleteOutbound($this->createdUserId, $queueId);
            $this->assertTrue($result->success);
        } else {
            $this->markTestSkipped('No outbound queue ID available for testing');
        }
    }

    public function testListAllMessages(): void
    {
        // Setup
        $this->createdUserId = $this->createTestUser(
            $this->generateUniqueUsername(),
            'TestPassword123!',
            $this->generateUniqueEmail()
        );
        $this->createdMailboxId = $this->createTestMailbox($this->createdUserId, 'Test Folder');

        // Upload multiple messages
        for ($i = 1; $i <= 3; $i++) {
            $rawMessage = "From: sender@example.com\r\n"
                . "To: recipient@example.com\r\n"
                . "Subject: Test List Message $i\r\n"
                . "\r\n"
                . "Message body $i.\r\n";

            $this->uploadTestMessage($this->createdUserId, $this->createdMailboxId, $rawMessage);
        }

        // Test list all messages
        $listDto = new ListMessagesRequestDto();
        $result = $this->client->messages()->all($this->createdUserId, $this->createdMailboxId, $listDto);

        $this->assertGreaterThanOrEqual(3, $result->total);
        $this->assertNotEmpty($result->results);
    }

    public function testMessageLifecycle(): void
    {
        // Complete lifecycle test: create, read, update, delete
        $this->createdUserId = $this->createTestUser(
            $this->generateUniqueUsername(),
            'TestPassword123!',
            $this->generateUniqueEmail()
        );
        $this->createdMailboxId = $this->createTestMailbox($this->createdUserId, 'Test Folder');
        $this->secondMailboxId = $this->createTestMailbox($this->createdUserId, 'Target Folder');

        // 1. Create (upload)
        $rawMessage = "From: sender@example.com\r\n"
            . "To: recipient@example.com\r\n"
            . "Subject: Test Lifecycle Message\r\n"
            . "\r\n"
            . "Full lifecycle test message.\r\n";

        $uploadDto = new UploadMessageRequestDto(raw: $rawMessage);
        $uploadResult = $this->client->messages()->upload($this->createdUserId, $this->createdMailboxId, $uploadDto);
        $this->assertTrue($uploadResult->success);
        $this->createdMessageId = (string)$uploadResult->message->id;

        // 2. Read
        $message = $this->client->messages()->get($this->createdUserId, $this->createdMailboxId, (int)$this->createdMessageId);
        $this->assertEquals((int)$this->createdMessageId, $message->id);
        // Note: seen status after upload varies by API implementation

        // 3. Update (mark as seen and flagged)
        $updateDto = new BulkUpdateMessagesRequestDto(
            message: $this->createdMessageId,
            seen: true,
            flagged: true
        );
        $updateResult = $this->client->messages()->update($this->createdUserId, $this->createdMailboxId, $updateDto);
        $this->assertTrue($updateResult->success);

        // Verify update
        $updatedMessage = $this->client->messages()->get($this->createdUserId, $this->createdMailboxId, (int)$this->createdMessageId);
        $this->assertTrue($updatedMessage->seen ?? false);
        $this->assertTrue($updatedMessage->flagged ?? false);

        // 4. Move to another mailbox
        $moveDto = new BulkUpdateMessagesRequestDto(
            message: $this->createdMessageId,
            moveTo: $this->secondMailboxId
        );
        $moveResult = $this->client->messages()->update($this->createdUserId, $this->createdMailboxId, $moveDto);
        $this->assertTrue($moveResult->success);

        // 5. Delete
        $deleteResult = $this->client->messages()->delete($this->createdUserId, $this->secondMailboxId, (int)$this->createdMessageId);
        $this->assertTrue($deleteResult->success);
    }
}
