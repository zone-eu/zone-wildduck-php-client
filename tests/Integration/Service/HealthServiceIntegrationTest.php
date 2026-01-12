<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\Health\HealthCheckResponseDto;

class HealthServiceIntegrationTest extends IntegrationTestCase
{
    public function testHealthCheck(): void
    {
        // Check health status
        $health = $this->client->health()->check();

        $this->assertInstanceOf(HealthCheckResponseDto::class, $health);
        $this->assertTrue($health->success);

        // Health check should return some status information
        // The exact structure depends on the API implementation
        // but at minimum it should indicate success
    }

    public function testHealthCheckMultipleTimes(): void
    {
        // Health check should be idempotent and consistently available
        for ($i = 0; $i < 3; $i++) {
            $health = $this->client->health()->check();

            $this->assertInstanceOf(HealthCheckResponseDto::class, $health);
            $this->assertTrue($health->success);
        }
    }
}
