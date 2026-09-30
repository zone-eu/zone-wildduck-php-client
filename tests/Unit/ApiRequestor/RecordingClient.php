<?php

declare(strict_types=1);

namespace Tests\Unit\ApiRequestor;

use Zone\Wildduck\HttpClient\ClientInterface;

/**
 * Records the raw request headers instead of talking to the network,
 * so the credential carriers can be asserted directly.
 */
class RecordingClient implements ClientInterface
{
    public array|null $lastHeaders = null;

    public function request(string $method, string $absUrl, array $headers, mixed $params, bool $hasFile): array
    {
        $this->lastHeaders = $headers;

        return ['{"success":true}', 200, ['X-RateLimit-Limit' => '100']];
    }
}
