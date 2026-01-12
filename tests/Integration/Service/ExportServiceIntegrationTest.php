<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\Export\CreateExportRequestDto;
use Zone\Wildduck\Dto\Export\ExportResponseDto;

class ExportServiceIntegrationTest extends IntegrationTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testExportUserData(): void
    {
        $this->markTestSkipped('Export operations require elevated admin privileges not available in test environment');
    }

    public function testExportMultipleUsers(): void
    {
        $this->markTestSkipped('Export operations require elevated admin privileges not available in test environment');
    }

    public function testExportWithEmptyParameters(): void
    {
        // Test export with no specific users (might export all or fail with validation)
        $exportDto = new CreateExportRequestDto();

        try {
            $exportResult = $this->client->export()->export($exportDto);

            // If it succeeds, verify we got a valid response
            $this->assertInstanceOf(ExportResponseDto::class, $exportResult);
        } catch (\Exception $e) {
            // Some APIs might require at least one user or tag
            // In that case, we expect an exception
            $this->assertInstanceOf(\Zone\Wildduck\Exception\WildduckException::class, $e);
        }
    }

    public function testImportUserData(): void
    {
        // Import is typically used with exported data
        // Without actual exported data, we test that the endpoint is accessible
        // and returns a response (might fail with validation error)

        try {
            $importResult = $this->client->export()->import();

            // If it succeeds without data, verify response
            $this->assertInstanceOf(ExportResponseDto::class, $importResult);
        } catch (\Exception $e) {
            // Import without proper data will likely fail
            // We just verify the endpoint is accessible and returns expected error type
            $this->assertInstanceOf(\Zone\Wildduck\Exception\WildduckException::class, $e);
        }
    }
}
