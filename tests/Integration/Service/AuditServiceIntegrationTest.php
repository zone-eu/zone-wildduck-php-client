<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\Audit\CreateAuditRequestDto;
use Zone\Wildduck\Dto\Audit\CreateAuditResponseDto;
use Zone\Wildduck\Dto\Audit\AuditResponseDto;

class AuditServiceIntegrationTest extends IntegrationTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testCreateAndGetAudit(): void
    {
        $this->markTestSkipped('Audit operations require elevated admin privileges not available in test environment');
    }

    public function testExportAudit(): void
    {
        $this->markTestSkipped('Audit operations require elevated admin privileges not available in test environment');
    }

    public function testCreateAuditWithoutTimeRange(): void
    {
        $this->markTestSkipped('Audit operations require elevated admin privileges not available in test environment');
    }
}
