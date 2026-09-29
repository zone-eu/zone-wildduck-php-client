<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\Authentication\AuthenticateRequestDto;
use Zone\Wildduck\Dto\ScopedToken\CreateScopedTokenResponseDto;
use Zone\Wildduck\Dto\Shared\SuccessResponseDto;
use Zone\Wildduck\Exception\MasterTokenRequiredException;
use Zone\Wildduck\Exception\ScopedTokenNotFoundException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\UnsupportedAuthScopeException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\WildduckClient;

/**
 * Requires a WildDuck instance running the ZMS-108 branch.
 */
class ScopedTokenServiceIntegrationTest extends IntegrationTestCase
{
    private ?string $createdUserId = null;
    private ?string $masterToken = null;
    private array $createdSessionIds = [];

    protected function tearDown(): void
    {
        if ($this->createdUserId !== null) {
            if ($this->masterToken !== null) {
                foreach ($this->createdSessionIds as $sessionId) {
                    try {
                        $this->userClient()->scopedTokens()->revokeScopedToken('mcp', $sessionId);
                    } catch (\Exception) {
                        // Session might already be revoked, ignore
                    }
                }
            }
            $this->createdSessionIds = [];
            $this->cleanupUser($this->createdUserId);
            $this->createdUserId = null;
        }
        $this->masterToken = null;

        parent::tearDown();
    }

    /**
     * Create a test user and return their master API token
     *
     * @return string The created user ID
     */
    private function createTestUserWithMasterToken(): string
    {
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';

        $this->createdUserId = $this->createTestUser($username, $password, $this->generateUniqueEmail());

        $authResult = $this->client->authentication()->authenticate(
            new AuthenticateRequestDto(
                username: $username,
                password: $password,
                scope: 'master',
                token: true
            )
        );

        $this->assertNotEmpty($authResult->token);
        $this->masterToken = $authResult->token;

        return $this->createdUserId;
    }

    private function userClient(): WildduckClient
    {
        return new WildduckClient([
            'api_base' => self::apiBaseUrl(),
            'access_token' => $this->masterToken,
        ]);
    }

    public function testScopedTokenLifecycle(): void
    {
        $this->createTestUserWithMasterToken();

        $createResult = $this->userClient()->scopedTokens()->createScopedToken('mcp');

        $this->assertInstanceOf(CreateScopedTokenResponseDto::class, $createResult);
        $this->assertTrue($createResult->success);
        $this->assertSame('mcp', $createResult->scope);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $createResult->id);
        $this->assertMatchesRegularExpression('/^wdmcp_\d[a-f0-9]{72}$/', $createResult->token);
        $this->createdSessionIds[] = $createResult->id;

        // Revocation takes the session identifier, not the one-time secret
        $revokeResult = $this->userClient()->scopedTokens()->revokeScopedToken('mcp', $createResult->id);

        $this->assertInstanceOf(SuccessResponseDto::class, $revokeResult);
        $this->assertTrue($revokeResult->success);
        $this->createdSessionIds = [];
    }

    public function testRevokeUnknownSessionFailsWithScopedTokenNotFound(): void
    {
        $this->createTestUserWithMasterToken();

        $unknownId = str_repeat('0', 64);

        try {
            $this->userClient()->scopedTokens()->revokeScopedToken('mcp', $unknownId);
            $this->fail('Expected a ScopedTokenNotFoundException for an unknown session id');
        } catch (ScopedTokenNotFoundException $exception) {
            // The symmetric route answers ScopedTokenNotFound; images still running
            // the MCP-specific route (ticket 08) answer the legacy McpTokenNotFound
            $this->assertContains(
                $exception->getErrorCode(),
                [ScopedTokenNotFoundException::ERROR_CODE, ScopedTokenNotFoundException::LEGACY_ERROR_CODE]
            );
        }
    }

    public function testRevokeRejectsMalformedSessionId(): void
    {
        $this->createTestUserWithMasterToken();

        $this->expectException(ValidationException::class);

        $this->userClient()->scopedTokens()->revokeScopedToken('mcp', 'not-a-64-char-hex-id');
    }

    public function testCreateRequiresMasterToken(): void
    {
        // The default client is authenticated with the global (root) API token
        $this->expectException(MasterTokenRequiredException::class);

        $this->client->scopedTokens()->createScopedToken('mcp');
    }

    public function testCreateRejectsUnsupportedScope(): void
    {
        $this->createTestUserWithMasterToken();

        $this->expectException(UnsupportedAuthScopeException::class);

        $this->userClient()->scopedTokens()->createScopedToken('imap');
    }

    public function testCreateRejectsMalformedScope(): void
    {
        $this->createTestUserWithMasterToken();

        $this->expectException(ValidationException::class);

        $this->userClient()->scopedTokens()->createScopedToken(str_repeat('a', 100));
    }

    public function testRevokeRequiresMasterToken(): void
    {
        // The default client is authenticated with the global (root) API token
        $this->expectException(MasterTokenRequiredException::class);

        $this->client->scopedTokens()->revokeScopedToken('mcp', str_repeat('0', 64));
    }

    /**
     * The symmetric revocation route dispatches through the scope-handler
     * registry, so an unsupported scope must be rejected with a typed error.
     *
     * Skipped while the wdt108 image still runs the MCP-specific
     * DELETE /authenticate/mcp/:token route, where other scopes answer
     * 404 ResourceNotFound. Runs as soon as the image ships ticket 08's
     * symmetric DELETE /authenticate/:scope/:token route.
     */
    public function testRevokeRejectsUnsupportedScope(): void
    {
        $this->createTestUserWithMasterToken();

        try {
            $this->userClient()->scopedTokens()->revokeScopedToken('imap', str_repeat('0', 64));
            $this->fail('Expected an UnsupportedAuthScopeException for an unsupported scope');
        } catch (UnsupportedAuthScopeException) {
            return;
        } catch (RequestFailedException $exception) {
            if ($exception->getErrorCode() === 'ResourceNotFound') {
                $this->markTestSkipped(
                    'wdt108 image still runs the MCP-specific revocation route; waiting for mcp-oauth ticket 08 (DELETE /authenticate/:scope/:token)'
                );
            }

            throw $exception;
        }
    }
}
