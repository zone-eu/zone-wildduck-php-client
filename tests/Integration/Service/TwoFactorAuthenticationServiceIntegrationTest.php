<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\TwoFactorAuth\GenerateTotpRequestDto;
use Zone\Wildduck\Dto\TwoFactorAuth\EnableCustomTwoFactorRequestDto;
use Zone\Wildduck\Dto\TwoFactorAuth\TotpSeedResponseDto;
use Zone\Wildduck\Dto\TwoFactorAuth\WebAuthnCredentialsResponseDto;
use Zone\Wildduck\Exception\RequestFailedException;

class TwoFactorAuthenticationServiceIntegrationTest extends IntegrationTestCase
{
    private ?string $createdUserId = null;

    protected function tearDown(): void
    {
        if ($this->createdUserId !== null) {
            $this->cleanupUser($this->createdUserId);
            $this->createdUserId = null;
        }

        parent::tearDown();
    }

    public function testGenerateTOTPSeed(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $address = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $address);

        // Generate TOTP seed
        $generateDto = new GenerateTotpRequestDto(
            issuer: 'Test Issuer',
            label: $username
        );

        $result = $this->client->twoFactor()->generateTOTPSeed($this->createdUserId, $generateDto);

        $this->assertInstanceOf(TotpSeedResponseDto::class, $result);
        $this->assertTrue($result->success);
        $this->assertNotEmpty($result->seed);
        $this->assertNotEmpty($result->qrcode);

        // Verify seed format (should be a base32 encoded string)
        $this->assertMatchesRegularExpression('/^[A-Z2-7]+$/', $result->seed);

        // Verify QR code is a data URI
        $this->assertStringStartsWith('data:image/png;base64,', $result->qrcode);
    }

    public function testDisableTOTPAuth(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $address = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $address);

        // Try to disable TOTP (should succeed even if not enabled, or throw expected error)
        try {
            $result = $this->client->twoFactor()->disableTOTPAuth($this->createdUserId);
            $this->assertTrue($result->success);
        } catch (\Zone\Wildduck\Exception\RequestFailedException $e) {
            // Expected if 2FA TOTP is not enabled
            $this->assertStringContainsString('2FA TOTP is not already disabled', $e->getMessage());
        }
    }

    public function testDisableAll2FA(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $address = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $address);

        // Disable all 2FA methods
        $result = $this->client->twoFactor()->disable($this->createdUserId);

        $this->assertTrue($result->success);
    }

    public function testCustomTwoFactorLifecycle(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $address = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $address);

        // Enable custom 2FA
        $enableDto = new EnableCustomTwoFactorRequestDto();
        $enableResult = $this->client->twoFactor()->enableCustom($this->createdUserId, $enableDto);

        $this->assertTrue($enableResult->success);

        // Disable custom 2FA
        $disableResult = $this->client->twoFactor()->disableCustom($this->createdUserId);

        $this->assertTrue($disableResult->success);
    }

    public function testWebAuthnCredentialsList(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $address = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $address);

        // Get WebAuthn credentials (should be empty for new user)
        $result = $this->client->twoFactor()->webAuthNCredentials($this->createdUserId);

        $this->assertInstanceOf(WebAuthnCredentialsResponseDto::class, $result);
        $this->assertTrue($result->success);
        $this->assertCount(0, $result->credentials);
    }

    public function testWebAuthnRemoveCredentialWithNonExistentId(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $address = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $address);

        // Try to remove a non-existent WebAuthn credential
        // This should fail gracefully
        $result = $this->client->twoFactor()->webAuthNRemoveCredential(
            $this->createdUserId,
            'aaaaaaaaaaaaaaaaaaaaaaaa'
        );

        $this->assertTrue($result->success);
    }

    public function testDisable2FAMethodsInSequence(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $address = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $address);

        // Disable TOTP (may fail if not enabled)
        try {
            $totpResult = $this->client->twoFactor()->disableTOTPAuth($this->createdUserId);
            $this->assertTrue($totpResult->success);
        } catch (\Zone\Wildduck\Exception\RequestFailedException $e) {
            // Expected if not enabled
        }

        // Disable custom 2FA (may fail if not enabled)
        try {
            $customResult = $this->client->twoFactor()->disableCustom($this->createdUserId);
            $this->assertTrue($customResult->success);
        } catch (\Zone\Wildduck\Exception\RequestFailedException $e) {
            // Expected if not enabled
        }

        // Disable all
        $allResult = $this->client->twoFactor()->disable($this->createdUserId);
        $this->assertTrue($allResult->success);
    }

    public function testGenerateTOTPSeedWithMinimalParams(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $address = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $address);

        // Generate TOTP seed with only required parameters
        $generateDto = new GenerateTotpRequestDto(
            issuer: 'TestApp'
        );

        $result = $this->client->twoFactor()->generateTOTPSeed($this->createdUserId, $generateDto);

        $this->assertInstanceOf(TotpSeedResponseDto::class, $result);
        $this->assertTrue($result->success);
        $this->assertNotEmpty($result->seed);
        $this->assertNotEmpty($result->qrcode);
    }

    public function testMultipleTOTPSeedGenerations(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $address = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $address);

        // Generate first TOTP seed
        $generateDto1 = new GenerateTotpRequestDto(
            issuer: 'Test Issuer 1',
            label: $username
        );

        $result1 = $this->client->twoFactor()->generateTOTPSeed($this->createdUserId, $generateDto1);
        $this->assertTrue($result1->success);
        $seed1 = $result1->seed;

        // Generate second TOTP seed
        $generateDto2 = new GenerateTotpRequestDto(
            issuer: 'Test Issuer 2',
            label: $username
        );

        $result2 = $this->client->twoFactor()->generateTOTPSeed($this->createdUserId, $generateDto2);
        $this->assertTrue($result2->success);
        $seed2 = $result2->seed;

        // Seeds may or may not be different depending on server implementation
        // Just verify both are valid
        $this->assertNotEmpty($seed1);
        $this->assertNotEmpty($seed2);
    }

    public function testEnableCustom2FAMultipleTimes(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $address = $this->generateUniqueEmail();

        $this->createdUserId = $this->createTestUser($username, $password, $address);

        $enableDto = new EnableCustomTwoFactorRequestDto();

        // Enable custom 2FA first time
        $result1 = $this->client->twoFactor()->enableCustom($this->createdUserId, $enableDto);
        $this->assertTrue($result1->success);

        // Enable custom 2FA again (server may reject if already enabled)
        try {
            $result2 = $this->client->twoFactor()->enableCustom($this->createdUserId, $enableDto);
            $this->assertTrue($result2->success);
        } catch (\Zone\Wildduck\Exception\RequestFailedException $e) {
            // Expected - already enabled
            $this->assertStringContainsString('already enabled', $e->getMessage());
        }

        // Disable it
        $disableResult = $this->client->twoFactor()->disableCustom($this->createdUserId);
        $this->assertTrue($disableResult->success);
    }
}
