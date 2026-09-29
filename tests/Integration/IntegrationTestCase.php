<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use Zone\Wildduck\WildduckClient;

/**
 * Base class for integration tests with WildDuck test server
 */
abstract class IntegrationTestCase extends TestCase
{
    protected static bool $serverStarted = false;
    protected static bool $serverStartedByUs = false;
    protected ?WildduckClient $client = null;

    public const DEFAULT_WILDDUCK_API_URL = 'http://localhost:9080';
    public const WILDDUCK_ACCESS_TOKEN = 'WDTESTSERVER';

    /**
     * Base URL of the WildDuck API under test.
     *
     * Honors the WILDDUCK_API_URL environment variable (see phpunit.xml) so
     * non-standard instances can be targeted, e.g. an isolated branch stack
     * running on another port.
     */
    protected static function apiBaseUrl(): string
    {
        $url = getenv('WILDDUCK_API_URL');

        return $url !== false && $url !== '' ? $url : self::DEFAULT_WILDDUCK_API_URL;
    }

    /**
     * Connect to the WildDuck API under test once for all tests.
     *
     * An already-running instance (e.g. an isolated branch stack targeted
     * via WILDDUCK_API_URL) is used as-is. Only the bundled test server,
     * started here for the default port, is stopped after the suite.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (self::$serverStarted) {
            return;
        }

        // A short probe first so external instances are not shadowed by a CLI start
        if (self::waitForApi(6)) {
            self::$serverStarted = true;
            return;
        }

        $vendorBin = __DIR__ . '/../../vendor/bin/wildduck-test-server';

        if (!file_exists($vendorBin)) {
            throw new \RuntimeException('WildDuck test server not found. Run: composer install');
        }

        // Start the bundled test server in the background (it always binds the default port)
        exec("$vendorBin start > /dev/null 2>&1 &");
        self::$serverStartedByUs = true;

        if (!self::waitForApi(60)) {
            throw new \RuntimeException('WildDuck test server failed to start within 30 seconds');
        }

        self::$serverStarted = true;
    }

    /**
     * Poll the API until it answers with a valid access token
     *
     * @param int $maxAttempts
     * @return bool
     */
    protected static function waitForApi(int $maxAttempts): bool
    {
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            try {
                $ch = curl_init(self::apiBaseUrl() . '/users');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Access-Token: ' . self::WILDDUCK_ACCESS_TOKEN]);
                curl_setopt($ch, CURLOPT_TIMEOUT, 1);
                $result = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode === 200) {
                    return true;
                }
            } catch (\Exception) {
                // Server not ready yet
            }

            usleep(500000); // Wait 0.5 seconds
        }

        return false;
    }

    /**
     * Stop the bundled test server after all tests, if we started it
     */
    public static function tearDownAfterClass(): void
    {
        if (self::$serverStartedByUs) {
            $vendorBin = __DIR__ . '/../../vendor/bin/wildduck-test-server';
            exec("$vendorBin stop > /dev/null 2>&1");
            self::$serverStartedByUs = false;
        }

        self::$serverStarted = false;

        parent::tearDownAfterClass();
    }

    /**
     * Set up WildDuck client before each test
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = new WildduckClient([
            'api_base' => self::apiBaseUrl(),
            'access_token' => self::WILDDUCK_ACCESS_TOKEN,
        ]);
    }

    /**
     * Clean up after each test
     */
    protected function tearDown(): void
    {
        $this->client = null;
        parent::tearDown();
    }

    /**
     * Generate a unique username for testing
     */
    protected function generateUniqueUsername(): string
    {
        return 'testuser_' . uniqid() . '_' . time();
    }

    /**
     * Generate a unique email address for testing
     */
    protected function generateUniqueEmail(string $domain = 'example.com'): string
    {
        return 'test_' . uniqid() . '@' . $domain;
    }

    /**
     * Clean up a user if it exists
     */
    protected function cleanupUser(string $userId): void
    {
        try {
            $this->client->users()->delete($userId);
        } catch (\Exception $e) {
            // User might not exist, ignore
        }
    }

    /**
     * Create a test user and return the user ID
     *
     * @param string $username The username for the test user
     * @param string $password The password for the test user
     * @param string $address The email address for the test user
     * @return string The created user ID
     */
    protected function createTestUser(string $username, string $password, string $address): string
    {
        $createDto = new \Zone\Wildduck\Dto\User\CreateUserRequestDto(
            username: $username,
            password: $password,
            address: $address
        );

        $result = $this->client->users()->create($createDto);
        return $result->id;
    }

    /**
     * Create a test mailbox and return the mailbox ID
     *
     * @param string $userId The user ID to create the mailbox for
     * @param string $mailboxPath The path/name of the mailbox
     * @return string The created mailbox ID
     */
    protected function createTestMailbox(string $userId, string $mailboxPath): string
    {
        // Add unique identifier to prevent conflicts when creating multiple mailboxes
        $uniquePath = $mailboxPath . '_' . uniqid();

        $createDto = new \Zone\Wildduck\Dto\Mailbox\CreateMailboxRequestDto(
            path: $uniquePath
        );

        $result = $this->client->mailboxes()->create($userId, $createDto);
        return $result->id;
    }

    /**
     * Upload a test message and return the message ID
     *
     * @param string $userId The user ID to upload the message for
     * @param string $mailboxId The mailbox ID to upload the message to
     * @param string $rawMessage The raw RFC822 formatted message
     * @return string The uploaded message ID
     */
    protected function uploadTestMessage(string $userId, string $mailboxId, string $rawMessage): string
    {
        $uploadDto = new \Zone\Wildduck\Dto\Message\UploadMessageRequestDto(
            raw: $rawMessage
        );

        $result = $this->client->messages()->upload($userId, $mailboxId, $uploadDto);
        return (string)$result->message->id;
    }

    /**
     * Delete a message (archives it immediately per WildDuck behavior)
     *
     * @param string $userId The user ID
     * @param string $mailboxId The mailbox ID
     * @param string $messageId The message ID to delete
     * @return void
     */
    protected function deleteMessage(string $userId, string $mailboxId, string $messageId): void
    {
        $this->client->messages()->delete($userId, $mailboxId, (int)$messageId);
    }

    /**
     * Get the absolute path to the shared test file fixture
     *
     * @return string The absolute path to test-file.txt
     */
    protected function getTestFilePath(): string
    {
        return __DIR__ . '/fixtures/test-file.txt';
    }
}
