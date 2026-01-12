<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\Submission\SubmitMessageRequestDto;
use Zone\Wildduck\Dto\Submission\SubmitMessageResponseDto;
use Zone\Wildduck\Dto\Shared\RecipientRequestDto;

class SubmissionServiceIntegrationTest extends IntegrationTestCase
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

    public function testSubmitMessage(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $email = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $email);

        // Prepare message submission
        $fromRecipient = new RecipientRequestDto(
            name: 'Test Sender',
            address: $email
        );

        $toRecipient = new RecipientRequestDto(
            name: 'Test Recipient',
            address: 'recipient@example.com'
        );

        $submitDto = new SubmitMessageRequestDto(
            from: $fromRecipient,
            to: [$toRecipient],
            subject: 'Test Message Submission',
            text: 'This is a test message submitted through the WildDuck API.',
            html: '<p>This is a <strong>test message</strong> submitted through the WildDuck API.</p>'
        );

        // Submit the message
        $result = $this->client->submission()->submit($this->createdUserId, $submitDto);

        // Verify submission result
        $this->assertInstanceOf(SubmitMessageResponseDto::class, $result);
        $this->assertTrue($result->success);
        $this->assertNotEmpty($result->message);

        // The message array should have an ID
        $this->assertArrayHasKey('id', $result->message);
        $this->assertNotEmpty($result->message['id']);
    }

    public function testSubmitMessageWithMultipleRecipients(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $email = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $email);

        // Prepare message with multiple recipients
        $fromRecipient = new RecipientRequestDto(
            address: $email
        );

        $toRecipients = [
            new RecipientRequestDto(address: 'recipient1@example.com'),
            new RecipientRequestDto(address: 'recipient2@example.com'),
        ];

        $ccRecipients = [
            new RecipientRequestDto(address: 'cc@example.com'),
        ];

        $submitDto = new SubmitMessageRequestDto(
            from: $fromRecipient,
            to: $toRecipients,
            cc: $ccRecipients,
            subject: 'Multi-recipient Test',
            text: 'Testing submission with multiple recipients.'
        );

        // Submit the message
        $result = $this->client->submission()->submit($this->createdUserId, $submitDto);

        // Verify submission
        $this->assertInstanceOf(SubmitMessageResponseDto::class, $result);
        $this->assertTrue($result->success);
    }

    public function testSubmitDraftMessage(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $email = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $email);

        // Submit as draft (not sent immediately)
        $fromRecipient = new RecipientRequestDto(
            address: $email
        );

        $toRecipient = new RecipientRequestDto(
            address: 'recipient@example.com'
        );

        $submitDto = new SubmitMessageRequestDto(
            from: $fromRecipient,
            to: [$toRecipient],
            subject: 'Draft Message',
            text: 'This is a draft message.',
            isDraft: true
        );

        // Submit the draft
        $result = $this->client->submission()->submit($this->createdUserId, $submitDto);

        // Verify submission
        $this->assertInstanceOf(SubmitMessageResponseDto::class, $result);
        $this->assertTrue($result->success);
    }
}
