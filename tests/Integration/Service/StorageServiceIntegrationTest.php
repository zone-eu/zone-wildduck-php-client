<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\Storage\ListFilesRequestDto;
use Zone\Wildduck\Dto\Storage\UploadFileRequestDto;

class StorageServiceIntegrationTest extends IntegrationTestCase
{
    private ?string $createdUserId = null;
    private ?string $createdFileId = null;

    protected function tearDown(): void
    {
        if ($this->createdUserId !== null) {
            $this->cleanupUser($this->createdUserId);
            $this->createdUserId = null;
            $this->createdFileId = null;
        }

        parent::tearDown();
    }

    public function testUploadFile(): void
    {
        // Setup
        $this->createdUserId = $this->createTestUser(
            $this->generateUniqueUsername(),
            'TestPassword123!',
            $this->generateUniqueEmail()
        );

        // Read test file
        $testFilePath = $this->getTestFilePath();
        $fileContent = file_get_contents($testFilePath);
        $this->assertNotFalse($fileContent, 'Test file should be readable');

        // Test upload
        $uploadDto = new UploadFileRequestDto(
            content: base64_encode($fileContent),
            filename: 'uploaded-test.txt',
            contentType: 'text/plain',
            encoding: 'base64'
        );

        $result = $this->client->storage()->upload($this->createdUserId, $uploadDto);

        $this->assertTrue($result->success);
        $this->createdFileId = $result->id;
    }

    public function testDownloadFile(): void
    {
        // Setup
        $this->createdUserId = $this->createTestUser(
            $this->generateUniqueUsername(),
            'TestPassword123!',
            $this->generateUniqueEmail()
        );

        // Upload a file first
        $testFilePath = $this->getTestFilePath();
        $originalContent = file_get_contents($testFilePath);
        $this->assertNotFalse($originalContent, 'Test file should be readable');

        $uploadDto = new UploadFileRequestDto(
            content: base64_encode($originalContent),
            filename: 'download-test.txt',
            contentType: 'text/plain',
            encoding: 'base64'
        );

        $uploadResult = $this->client->storage()->upload($this->createdUserId, $uploadDto);
        $this->createdFileId = $uploadResult->id;

        // Test download
        $downloadedContent = $this->client->storage()->download($this->createdUserId, $this->createdFileId);

        $this->assertNotEmpty($downloadedContent);
        $this->assertEquals($originalContent, $downloadedContent, 'Downloaded content should match original');
        $this->assertEquals(strlen($originalContent), strlen($downloadedContent), 'Downloaded size should match original size');
    }

    public function testDeleteFile(): void
    {
        // Setup
        $this->createdUserId = $this->createTestUser(
            $this->generateUniqueUsername(),
            'TestPassword123!',
            $this->generateUniqueEmail()
        );

        // Upload a file first
        $testFilePath = $this->getTestFilePath();
        $fileContent = file_get_contents($testFilePath);
        $this->assertNotFalse($fileContent, 'Test file should be readable');

        $uploadDto = new UploadFileRequestDto(
            content: base64_encode($fileContent),
            filename: 'delete-test.txt',
            contentType: 'text/plain',
            encoding: 'base64'
        );

        $uploadResult = $this->client->storage()->upload($this->createdUserId, $uploadDto);
        $fileId = $uploadResult->id;

        // Test delete
        $result = $this->client->storage()->delete($this->createdUserId, $fileId);

        $this->assertTrue($result->success);
    }

    public function testListAllFiles(): void
    {
        // Setup
        $this->createdUserId = $this->createTestUser(
            $this->generateUniqueUsername(),
            'TestPassword123!',
            $this->generateUniqueEmail()
        );

        // Upload multiple files
        $testFilePath = $this->getTestFilePath();
        $fileContent = file_get_contents($testFilePath);
        $this->assertNotFalse($fileContent, 'Test file should be readable');

        $fileIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $uploadDto = new UploadFileRequestDto(
                content: base64_encode($fileContent),
                filename: "list-test-$i.txt",
                contentType: 'text/plain',
                encoding: 'base64'
            );

            $uploadResult = $this->client->storage()->upload($this->createdUserId, $uploadDto);
            $fileIds[] = $uploadResult->id;
        }

        // Store first file ID for cleanup
        $this->createdFileId = $fileIds[0];

        // Test list all files
        $listDto = new ListFilesRequestDto();
        $result = $this->client->storage()->all($this->createdUserId, $listDto);

        $this->assertGreaterThanOrEqual(3, $result->total);
        $this->assertNotEmpty($result->results);

        // Verify our files are in the list
        $resultFileIds = array_map(fn($file) => $file->id, $result->results);
        foreach ($fileIds as $fileId) {
            $this->assertContains($fileId, $resultFileIds, "Uploaded file $fileId should be in the list");
        }
    }

    public function testFileLifecycle(): void
    {
        // Complete lifecycle test: upload, list, download, verify, delete
        $this->createdUserId = $this->createTestUser(
            $this->generateUniqueUsername(),
            'TestPassword123!',
            $this->generateUniqueEmail()
        );

        // 1. Upload
        $testFilePath = $this->getTestFilePath();
        $originalContent = file_get_contents($testFilePath);
        $this->assertNotFalse($originalContent, 'Test file should be readable');
        $originalSize = strlen($originalContent);

        $uploadDto = new UploadFileRequestDto(
            content: base64_encode($originalContent),
            filename: 'lifecycle-test.txt',
            contentType: 'text/plain',
            encoding: 'base64'
        );

        $uploadResult = $this->client->storage()->upload($this->createdUserId, $uploadDto);
        $this->assertTrue($uploadResult->success);
        $this->createdFileId = $uploadResult->id;

        // 2. List and verify it exists
        $listResult = $this->client->storage()->all($this->createdUserId, new ListFilesRequestDto());
        $this->assertGreaterThanOrEqual(1, $listResult->total);

        $found = false;
        $foundFile = null;
        foreach ($listResult->results as $file) {
            if ($file->id === $this->createdFileId) {
                $found = true;
                $foundFile = $file;
                break;
            }
        }
        $this->assertTrue($found, 'Uploaded file should be in the list');
        $this->assertEquals('lifecycle-test.txt', $foundFile->filename);
        $this->assertEquals('text/plain', $foundFile->contentType);
        $this->assertEquals($originalSize, $foundFile->size);

        // 3. Download
        $downloadedContent = $this->client->storage()->download($this->createdUserId, $this->createdFileId);
        $this->assertEquals($originalContent, $downloadedContent, 'Downloaded content should match original exactly');
        $this->assertEquals($originalSize, strlen($downloadedContent), 'Downloaded size should be exactly 41 bytes');

        // 4. Verify binary integrity (byte-for-byte match)
        $this->assertSame(
            md5($originalContent),
            md5($downloadedContent),
            'MD5 hash of downloaded content should match original'
        );

        // 5. Delete
        $deleteResult = $this->client->storage()->delete($this->createdUserId, $this->createdFileId);
        $this->assertTrue($deleteResult->success);

        // 6. Verify deletion - file should not be in list anymore (or list should have fewer items)
        $listAfterDelete = $this->client->storage()->all($this->createdUserId, new ListFilesRequestDto());
        $this->assertLessThan($listResult->total, $listAfterDelete->total);

        $stillFound = false;
        foreach ($listAfterDelete->results as $file) {
            if ($file->id === $this->createdFileId) {
                $stillFound = true;
                break;
            }
        }
        $this->assertFalse($stillFound, 'Deleted file should not be in the list');
    }

    public function testBinaryContentValidation(): void
    {
        // Test that validates exact binary content integrity
        $this->createdUserId = $this->createTestUser(
            $this->generateUniqueUsername(),
            'TestPassword123!',
            $this->generateUniqueEmail()
        );

        // Read the shared test file
        $testFilePath = $this->getTestFilePath();
        $expectedContent = file_get_contents($testFilePath);
        $this->assertNotFalse($expectedContent, 'Test file should be readable');
        $expectedSize = 41; // Known size from requirement
        $expectedMimeType = 'text/plain';

        // Verify test file properties
        $this->assertEquals($expectedSize, strlen($expectedContent), 'Test file should be exactly 41 bytes');

        // Upload
        $uploadDto = new UploadFileRequestDto(
            content: base64_encode($expectedContent),
            filename: 'binary-validation-test.txt',
            contentType: $expectedMimeType,
            encoding: 'base64'
        );

        $uploadResult = $this->client->storage()->upload($this->createdUserId, $uploadDto);
        $this->createdFileId = $uploadResult->id;

        // Download and validate
        $downloadedContent = $this->client->storage()->download($this->createdUserId, $this->createdFileId);

        // Validate exact byte match
        $this->assertSame($expectedContent, $downloadedContent, 'Downloaded content must match original byte-for-byte');
        $this->assertEquals($expectedSize, strlen($downloadedContent), 'Downloaded file must be exactly 41 bytes');

        // Validate each byte
        for ($i = 0; $i < $expectedSize; $i++) {
            $this->assertEquals(
                ord($expectedContent[$i]),
                ord($downloadedContent[$i]),
                "Byte at position $i should match"
            );
        }

        // Validate checksums
        $this->assertEquals(
            hash('sha256', $expectedContent),
            hash('sha256', $downloadedContent),
            'SHA256 hash should match'
        );
    }

    public function testUploadWithDifferentEncodings(): void
    {
        // Test uploading with different content types
        $this->createdUserId = $this->createTestUser(
            $this->generateUniqueUsername(),
            'TestPassword123!',
            $this->generateUniqueEmail()
        );

        $testFilePath = $this->getTestFilePath();
        $originalContent = file_get_contents($testFilePath);
        $this->assertNotFalse($originalContent, 'Test file should be readable');

        // Test 1: Upload with base64 encoding
        $uploadDto1 = new UploadFileRequestDto(
            content: base64_encode($originalContent),
            filename: 'encoding-base64.txt',
            contentType: 'text/plain',
            encoding: 'base64'
        );

        $result1 = $this->client->storage()->upload($this->createdUserId, $uploadDto1);
        $this->assertTrue($result1->success);
        $this->createdFileId = $result1->id;

        // Download and verify
        $downloaded1 = $this->client->storage()->download($this->createdUserId, $result1->id);
        $this->assertEquals($originalContent, $downloaded1);

        // Test 2: Upload without explicit encoding (treated as base64 by default)
        $uploadDto2 = new UploadFileRequestDto(
            content: base64_encode($originalContent),
            filename: 'encoding-default.txt',
            contentType: 'text/plain'
        );

        $result2 = $this->client->storage()->upload($this->createdUserId, $uploadDto2);
        $this->assertTrue($result2->success);

        // Download and verify
        $downloaded2 = $this->client->storage()->download($this->createdUserId, $result2->id);

        // decode from base64 if needed and compare
        $decoded2 = base64_decode($downloaded2, true);
        $this->assertNotFalse($decoded2, 'Downloaded content should be valid base64');
        $this->assertEquals($originalContent, $decoded2);
    }

    public function testListFilesWithPagination(): void
    {
        // Test listing files with pagination parameters
        $this->createdUserId = $this->createTestUser(
            $this->generateUniqueUsername(),
            'TestPassword123!',
            $this->generateUniqueEmail()
        );

        // Upload 5 files
        $testFilePath = $this->getTestFilePath();
        $fileContent = file_get_contents($testFilePath);
        $this->assertNotFalse($fileContent, 'Test file should be readable');

        $fileIds = [];
        for ($i = 1; $i <= 5; $i++) {
            $uploadDto = new UploadFileRequestDto(
                content: base64_encode($fileContent),
                filename: "pagination-test-$i.txt",
                contentType: 'text/plain',
                encoding: 'base64'
            );

            $result = $this->client->storage()->upload($this->createdUserId, $uploadDto);
            $fileIds[] = $result->id;
        }

        $this->createdFileId = $fileIds[0];

        // Test list with limit
        $listDto = new ListFilesRequestDto(limit: 3);
        $result = $this->client->storage()->all($this->createdUserId, $listDto);

        $this->assertGreaterThanOrEqual(5, $result->total);
        $this->assertLessThanOrEqual(3, count($result->results));

        // Test list with next cursor if available
        if ($result->nextCursor !== false && $result->nextCursor !== null) {
            $nextListDto = new ListFilesRequestDto(next: $result->nextCursor);
            $nextResult = $this->client->storage()->all($this->createdUserId, $nextListDto);

            $this->assertNotEmpty($nextResult->results);
        }
    }
}
