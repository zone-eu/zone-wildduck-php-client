<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\Webhook\CreateWebhookRequestDto;
use Zone\Wildduck\Dto\Webhook\CreateWebhookResponseDto;
use Zone\Wildduck\Dto\PaginatedResultDto;
use Zone\Wildduck\Dto\Shared\SuccessResponseDto;

class WebhookServiceIntegrationTest extends IntegrationTestCase
{
    private ?string $createdWebhookId = null;

    protected function tearDown(): void
    {
        if ($this->createdWebhookId !== null) {
            try {
                $this->client->webhooks()->delete($this->createdWebhookId);
            } catch (\Exception $e) {
                // Ignore cleanup errors
            }
            $this->createdWebhookId = null;
        }

        parent::tearDown();
    }

    public function testWebhookLifecycle(): void
    {
        // Create webhook
        $createDto = new CreateWebhookRequestDto(
            type: ['message.new', 'message.deleted'],
            url: 'https://example.com/webhook/test'
        );

        $createResult = $this->client->webhooks()->create($createDto);

        $this->assertInstanceOf(CreateWebhookResponseDto::class, $createResult);
        $this->assertTrue($createResult->success);
        $this->assertNotEmpty($createResult->id);

        $this->createdWebhookId = $createResult->id;

        // List webhooks
        $webhooks = $this->client->webhooks()->all();

        $this->assertInstanceOf(PaginatedResultDto::class, $webhooks);
        $this->assertGreaterThan(0, $webhooks->total);

        // Find our created webhook in the list
        $found = false;
        foreach ($webhooks->results as $webhook) {
            if ($webhook->id === $this->createdWebhookId) {
                $found = true;
                $this->assertEquals('https://example.com/webhook/test', $webhook->url);
                $this->assertContains('message.new', $webhook->type);
                $this->assertContains('message.deleted', $webhook->type);
                break;
            }
        }

        $this->assertTrue($found, 'Created webhook should be in the list');

        // Delete webhook
        $deleteResult = $this->client->webhooks()->delete($this->createdWebhookId);

        $this->assertInstanceOf(SuccessResponseDto::class, $deleteResult);
        $this->assertTrue($deleteResult->success);

        $this->createdWebhookId = null; // Already deleted
    }

    public function testListEmptyWebhooks(): void
    {
        // List webhooks (may be empty or contain existing webhooks)
        $webhooks = $this->client->webhooks()->all();

        $this->assertInstanceOf(PaginatedResultDto::class, $webhooks);
    }

    public function testCreateWebhookWithMultipleEventTypes(): void
    {
        // Create webhook with various event types
        $createDto = new CreateWebhookRequestDto(
            type: ['user.created', 'user.deleted', 'mailbox.created'],
            url: 'https://example.com/webhook/events'
        );

        $createResult = $this->client->webhooks()->create($createDto);

        $this->assertInstanceOf(CreateWebhookResponseDto::class, $createResult);
        $this->assertTrue($createResult->success);
        $this->assertNotEmpty($createResult->id);

        $this->createdWebhookId = $createResult->id;

        // Verify webhook was created
        $webhooks = $this->client->webhooks()->all();
        $found = false;

        foreach ($webhooks->results as $webhook) {
            if ($webhook->id === $this->createdWebhookId) {
                $found = true;
                $this->assertContains('user.created', $webhook->type);
                $this->assertContains('user.deleted', $webhook->type);
                $this->assertContains('mailbox.created', $webhook->type);
                break;
            }
        }

        $this->assertTrue($found);
    }
}
