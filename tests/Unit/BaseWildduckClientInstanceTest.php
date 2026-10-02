<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tests\Unit\ApiRequestor\RecordingClient;
use Zone\Wildduck\ApiRequestor;
use Zone\Wildduck\HttpClient\CurlClient;
use Zone\Wildduck\WildduckClient;

/**
 * The entry point constructs a fresh client from exactly the config given
 * on every call: no cross-call state, no merging with any earlier call.
 */
class BaseWildduckClientInstanceTest extends TestCase
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

    private function headerValue(string $name): string|null
    {
        foreach ($this->client->lastHeaders ?? [] as $line) {
            if (str_starts_with($line, $name . ':')) {
                return trim(substr($line, strlen($name) + 1));
            }
        }

        return null;
    }

    public function testSecondInstanceCallSeesNoConfigFromEarlierCall(): void
    {
        $first = WildduckClient::instance([
            'api_base' => 'http://localhost:9080',
            'access_token' => 'first_token',
        ]);

        $second = WildduckClient::instance(['access_token' => 'second_token']);

        // Per-call isolation: a fresh client, built from exactly the second
        // call's config (defaults fill the rest, nothing from the first call).
        $this->assertNotSame($first, $second);
        $this->assertSame('https://localhost:8080', $second->getApiBase());
        $this->assertSame('second_token', $second->getAccessToken());

        // The earlier call's client is untouched by the later one.
        $this->assertSame('http://localhost:9080', $first->getApiBase());
        $this->assertSame('first_token', $first->getAccessToken());
    }

    public function testSecondCallSeesNoCarrierFromFirstCall(): void
    {
        WildduckClient::bearerToken('wdmcp_secret');

        $client = WildduckClient::instance([
            'api_base' => 'http://localhost:9080',
            'access_token' => 'regular_token',
        ]);
        $client->request('get', '/users/me', [], []);

        // The bearer carrier from the earlier factory call does not leak into
        // this header-mode client.
        $this->assertSame('regular_token', $this->headerValue('X-Access-Token'));
        $this->assertNull($this->headerValue('Authorization'));
    }
}
