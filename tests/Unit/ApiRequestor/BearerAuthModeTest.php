<?php

declare(strict_types=1);

namespace Tests\Unit\ApiRequestor;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\ApiRequestor;
use Zone\Wildduck\BaseWildduckClient;
use Zone\Wildduck\Exception\InvalidArgumentException;
use Zone\Wildduck\HttpClient\CurlClient;
use Zone\Wildduck\WildduckClient;

/**
 * Pins the credential carriers: the X-Access-Token header (every token
 * class, unchanged) and the opt-in bearer mode, which is the only carrier
 * WildDuck accepts for MCP (wdmcp_) credentials.
 */
class BearerAuthModeTest extends TestCase
{
    private RecordingClient $client;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = new RecordingClient();
        ApiRequestor::setHttpClient($this->client);
    }

    #[\Override]
    protected function tearDown(): void
    {
        ApiRequestor::setHttpClient(CurlClient::instance());

        parent::tearDown();
    }

    /**
     * @return list<string> the raw headers of the recorded request
     */
    private function headerLines(): array
    {
        return $this->client->lastHeaders ?? [];
    }

    private function headerValue(string $name): string|null
    {
        foreach ($this->headerLines() as $line) {
            if (str_starts_with($line, $name . ':')) {
                return trim(substr($line, strlen($name) + 1));
            }
        }

        return null;
    }

    /**
     * @return list<string> the header names of the recorded request, in order
     */
    private function headerNames(): array
    {
        return array_map(static fn (string $line): string => explode(':', $line, 2)[0], $this->headerLines());
    }

    public function testDefaultModeSendsXAccessTokenAndNoAuthorizationHeader(): void
    {
        $requestor = new ApiRequestor('wd_test_token', 'http://localhost:9080');
        $requestor->request('get', '/users/me');

        $this->assertSame('wd_test_token', $this->headerValue('X-Access-Token'));
        $this->assertNull($this->headerValue('Authorization'));
        $this->assertNotSame(null, $this->headerValue('User-Agent'));
        $this->assertNotSame(null, $this->headerValue('X-Wildduck-Client-User-Agent'));
    }

    public function testDefaultModeHeaderSetIsUnchanged(): void
    {
        $requestor = new ApiRequestor('wd_test_token', 'http://localhost:9080');
        $requestor->request('get', '/users/me');

        // The exact default carrier set: the bearer mode must not leak
        // into ordinary token classes.
        $this->assertSame(
            ['X-Wildduck-Client-User-Agent', 'User-Agent', 'X-Access-Token', 'Content-Type'],
            $this->headerNames()
        );
    }

    public function testBearerModeSendsAuthorizationHeaderAndNoXAccessToken(): void
    {
        $requestor = new ApiRequestor('wdmcp_test_token', 'http://localhost:9080', ApiRequestor::AUTH_MODE_BEARER);
        $requestor->request('get', '/users/me');

        $this->assertSame('Bearer wdmcp_test_token', $this->headerValue('Authorization'));
        $this->assertNull($this->headerValue('X-Access-Token'));
        $this->assertNotSame(null, $this->headerValue('User-Agent'));
        $this->assertNotSame(null, $this->headerValue('X-Wildduck-Client-User-Agent'));
    }

    public function testBearerModeHeaderSetIsTheDefaultSetSwappedForAuthorization(): void
    {
        $requestor = new ApiRequestor('wdmcp_test_token', 'http://localhost:9080', ApiRequestor::AUTH_MODE_BEARER);
        $requestor->request('get', '/users/me');

        $this->assertSame(
            ['X-Wildduck-Client-User-Agent', 'User-Agent', 'Authorization', 'Content-Type'],
            $this->headerNames()
        );
    }

    public function testBearerTokenFactoryPresentsTheTokenAsBearer(): void
    {
        $client = BaseWildduckClient::bearerToken('wdmcp_secret_token');
        $client->request('get', '/users/me', [], []);

        $this->assertSame('Bearer wdmcp_secret_token', $this->headerValue('Authorization'));
        $this->assertNull($this->headerValue('X-Access-Token'));
    }

    public function testTokenFactoryStillPresentsTheTokenInTheAccessTokenHeader(): void
    {
        $client = BaseWildduckClient::token('regular_master_token');
        $client->request('get', '/users/me', [], []);

        $this->assertSame('regular_master_token', $this->headerValue('X-Access-Token'));
        $this->assertNull($this->headerValue('Authorization'));
    }

    public function testFullConfigWithBearerAuthModeIsAccepted(): void
    {
        $client = WildduckClient::instance([
            'api_base' => 'http://localhost:9080',
            'access_token' => 'wdmcp_configured_token',
            'auth_mode' => ApiRequestor::AUTH_MODE_BEARER,
        ]);
        $client->request('get', '/users/me', [], []);

        $this->assertSame('Bearer wdmcp_configured_token', $this->headerValue('Authorization'));
        $this->assertNull($this->headerValue('X-Access-Token'));
    }

    public function testUnknownAuthModeIsRejected(): void
    {
        try {
            new WildduckClient([
                'api_base' => 'http://localhost:9080',
                'access_token' => 'wd_test_token',
                'auth_mode' => 'cookie',
            ]);
            $this->fail('Expected an InvalidArgumentException for an unknown auth_mode');
        } catch (InvalidArgumentException $exception) {
            $this->assertSame('auth_mode must be "access_token" or "bearer"', $exception->getMessage());
        }
    }
}
