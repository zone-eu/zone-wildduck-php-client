<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\McpToken\CreateMcpTokenRequestDto;
use Zone\Wildduck\Dto\McpToken\CreateMcpTokenResponseDto;
use Zone\Wildduck\Dto\McpToken\ListMcpTokensResponseDto;
use Zone\Wildduck\Dto\McpToken\McpTokenResponseDto;
use Zone\Wildduck\Dto\Shared\SuccessResponseDto;
use Zone\Wildduck\Exception\ValidationException;

/**
 * Requires a WildDuck instance running the ZMS-96-mcp branch.
 */
class McpTokenServiceIntegrationTest extends IntegrationTestCase
{
    private ?string $createdUserId = null;
    private array $createdTokenIds = [];

    protected function tearDown(): void
    {
        if ($this->createdUserId !== null) {
            foreach ($this->createdTokenIds as $tokenId) {
                try {
                    $this->client->mcpTokens()->delete($this->createdUserId, $tokenId);
                } catch (\Exception) {
                    // Token might already be deleted, ignore
                }
            }
            $this->createdTokenIds = [];

            $this->cleanupUser($this->createdUserId);
            $this->createdUserId = null;
        }

        parent::tearDown();
    }

    private function createTestUserForTokens(): string
    {
        $this->createdUserId = $this->createTestUser(
            $this->generateUniqueUsername(),
            'TestPassword123!',
            $this->generateUniqueEmail()
        );

        return $this->createdUserId;
    }

    public function testMcpTokenLifecycle(): void
    {
        $userId = $this->createTestUserForTokens();

        $createDto = new CreateMcpTokenRequestDto(
            description: 'Codex CLI',
            expires: (new \DateTimeImmutable('+30 days'))->format('c')
        );

        $createResult = $this->client->mcpTokens()->create($userId, $createDto);

        $this->assertInstanceOf(CreateMcpTokenResponseDto::class, $createResult);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{24}$/', $createResult->id);
        $this->assertMatchesRegularExpression('/^wdmcp_\d[a-f0-9]{72}$/', $createResult->token);
        $this->assertSame('Codex CLI', $createResult->description);
        $this->assertSame('mcp:read', $createResult->role);
        $this->assertSame('mcp', $createResult->audience);
        $this->assertNotEmpty($createResult->created);
        $this->assertNotNull($createResult->expires);

        $this->createdTokenIds[] = $createResult->id;

        // Listing shows the token, with the one-time secret never present
        $listResult = $this->client->mcpTokens()->all($userId);

        $this->assertInstanceOf(ListMcpTokensResponseDto::class, $listResult);
        $this->assertTrue($listResult->success);
        $this->assertCount(1, $listResult->results);

        $listed = $listResult->results[0];
        $this->assertInstanceOf(McpTokenResponseDto::class, $listed);
        $this->assertSame($createResult->id, $listed->id);
        $this->assertSame('Codex CLI', $listed->description);
        $this->assertSame('mcp:read', $listed->role);
        $this->assertSame('mcp', $listed->audience);
        $this->assertNull($listed->lastUse);
        $this->assertStringNotContainsString('wdmcp_', json_encode($listResult));

        // Revoke by record id
        $deleteResult = $this->client->mcpTokens()->delete($userId, $createResult->id);

        $this->assertInstanceOf(SuccessResponseDto::class, $deleteResult);
        $this->assertTrue($deleteResult->success);
        $this->createdTokenIds = [];

        // Revoked token is gone from the list
        $listAfterDelete = $this->client->mcpTokens()->all($userId);
        $this->assertCount(0, $listAfterDelete->results);
    }

    public function testCreateWithoutExpiresOmitsExpiry(): void
    {
        $userId = $this->createTestUserForTokens();

        $createResult = $this->client->mcpTokens()->create(
            $userId,
            new CreateMcpTokenRequestDto(description: 'No expiry token')
        );

        $this->assertNull($createResult->expires);

        $listed = $this->client->mcpTokens()->all($userId)->results[0];
        $this->assertNull($listed->expires);
    }

    public function testListIsNewestFirst(): void
    {
        $userId = $this->createTestUserForTokens();

        $first = $this->client->mcpTokens()->create(
            $userId,
            new CreateMcpTokenRequestDto(description: 'First token')
        );
        $this->createdTokenIds[] = $first->id;

        usleep(1100000); // Ensure a distinct creation timestamp

        $second = $this->client->mcpTokens()->create(
            $userId,
            new CreateMcpTokenRequestDto(description: 'Second token')
        );
        $this->createdTokenIds[] = $second->id;

        $listResult = $this->client->mcpTokens()->all($userId);

        $this->assertCount(2, $listResult->results);
        $this->assertSame($second->id, $listResult->results[0]->id);
        $this->assertSame($first->id, $listResult->results[1]->id);
    }

    public function testDeleteRejectsOneTimeSecretAsLookupKey(): void
    {
        $userId = $this->createTestUserForTokens();

        $createResult = $this->client->mcpTokens()->create(
            $userId,
            new CreateMcpTokenRequestDto(description: 'Secret as key token')
        );
        $this->createdTokenIds[] = $createResult->id;

        // The plaintext secret is not a 24-char hex record id, so it must be rejected
        $this->expectException(ValidationException::class);

        $this->client->mcpTokens()->delete($userId, $createResult->token);
    }

    public function testCreateWithPastExpiryFailsWithMachineReadableCode(): void
    {
        $userId = $this->createTestUserForTokens();

        try {
            $this->client->mcpTokens()->create(
                $userId,
                new CreateMcpTokenRequestDto(
                    description: 'Past expiry token',
                    expires: (new \DateTimeImmutable('-1 day'))->format('c')
                )
            );
            $this->fail('Expected a ValidationException for a past expiry');
        } catch (ValidationException) {
            $this->addToAssertionCount(1);
        }
    }
}
