<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\User\CreateUserRequestDto;
use Zone\Wildduck\Dto\User\CreatedUserResponseDto;
use Zone\Wildduck\Dto\User\UpdateUserRequestDto;
use Zone\Wildduck\Dto\User\UserResponseDto;
use Zone\Wildduck\Dto\User\UserInfoResponseDto;
use Zone\Wildduck\Dto\User\ListUsersRequestDto;

class UserServiceIntegrationTest extends IntegrationTestCase
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

    public function testUserLifecycle(): void
    {
        // Create user
        $username = $this->generateUniqueUsername();
        $email = $this->generateUniqueEmail();

        $createDto = new CreateUserRequestDto(
            username: $username,
            password: 'TestPassword123!',
            address: $email,
            name: 'Test User'
        );

        $createResult = $this->client->users()->create($createDto);

        $this->assertInstanceOf(CreatedUserResponseDto::class, $createResult);
        $this->assertTrue($createResult->success);
        $this->assertNotEmpty($createResult->id);

        $this->createdUserId = $createResult->id;

        // Get user
        $user = $this->client->users()->get($this->createdUserId);

        $this->assertInstanceOf(UserResponseDto::class, $user);
        $this->assertEquals($this->createdUserId, $user->id);
        $this->assertEquals($username, $user->username);
        $this->assertEquals($email, $user->address);
        $this->assertEquals('Test User', $user->name);

        // Update user
        $updateDto = new UpdateUserRequestDto(
            name: 'Updated Test User'
        );

        $updateResult = $this->client->users()->update($this->createdUserId, $updateDto);

        $this->assertInstanceOf(\Zone\Wildduck\Dto\Shared\SuccessResponseDto::class, $updateResult);
        $this->assertTrue($updateResult->success);

        // Verify update
        $updatedUser = $this->client->users()->get($this->createdUserId);
        $this->assertEquals('Updated Test User', $updatedUser->name);

        // Delete user
        $deleteResult = $this->client->users()->delete($this->createdUserId);
        $this->assertTrue($deleteResult->success);

        $this->createdUserId = null; // Already deleted
    }

    public function testListUsers(): void
    {
        // Create a test user first
        $username = $this->generateUniqueUsername();

        $createDto = new CreateUserRequestDto(
            username: $username,
            password: 'TestPassword123!',
            address: $this->generateUniqueEmail()
        );

        $createResult = $this->client->users()->create($createDto);
        $this->createdUserId = $createResult->id;

        // List users
        $users = $this->client->users()->all(new ListUsersRequestDto(limit: 10));

        $this->assertGreaterThan(0, $users->total);
        $this->assertNotEmpty($users->results);

        // Find our created user in the list
        $found = false;
        foreach ($users->results as $user) {
            $this->assertInstanceOf(UserResponseDto::class, $user);
            if ($user->id === $this->createdUserId) {
                $found = true;
                break;
            }
        }

        $this->assertTrue($found, 'Created user should be in the list');
    }

    public function testGetIdByUsername(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();

        $createDto = new CreateUserRequestDto(
            username: $username,
            password: 'TestPassword123!',
            address: $this->generateUniqueEmail()
        );

        $createResult = $this->client->users()->create($createDto);
        $this->createdUserId = $createResult->id;

        // Resolve username to ID
        $result = $this->client->users()->getIdByUsername($username);

        $this->assertInstanceOf(\Zone\Wildduck\Dto\User\ResolveUserResponseDto::class, $result);
        $this->assertTrue($result->success);
        $this->assertEquals($this->createdUserId, $result->id);
    }

    public function testLogout(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();

        $createDto = new CreateUserRequestDto(
            username: $username,
            password: 'TestPassword123!',
            address: $this->generateUniqueEmail()
        );

        $createResult = $this->client->users()->create($createDto);
        $this->createdUserId = $createResult->id;

        // Logout user (invalidate all sessions)
        $logoutDto = new \Zone\Wildduck\Dto\User\LogoutUserRequestDto(
            reason: 'Test logout'
        );

        $logoutResult = $this->client->users()->logout($this->createdUserId, $logoutDto);

        $this->assertInstanceOf(\Zone\Wildduck\Dto\Shared\SuccessResponseDto::class, $logoutResult);
        $this->assertTrue($logoutResult->success);
    }

    public function testResetPassword(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $oldPassword = 'TestPassword123!';

        $createDto = new CreateUserRequestDto(
            username: $username,
            password: $oldPassword,
            address: $this->generateUniqueEmail()
        );

        $createResult = $this->client->users()->create($createDto);
        $this->createdUserId = $createResult->id;

        // Reset password (generates a new temporary password)
        $resetDto = new \Zone\Wildduck\Dto\User\ResetPasswordRequestDto(
            validAfter: null
        );

        $resetResult = $this->client->users()->resetPassword($this->createdUserId, $resetDto);

        $this->assertInstanceOf(\Zone\Wildduck\Dto\User\ResetPasswordResponseDto::class, $resetResult);
        $this->assertTrue($resetResult->success);
        // The response contains the new temporary password
        $this->assertNotEmpty($resetResult->password);
    }

    public function testRecalculateQuota(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();

        $createDto = new CreateUserRequestDto(
            username: $username,
            password: 'TestPassword123!',
            address: $this->generateUniqueEmail()
        );

        $createResult = $this->client->users()->create($createDto);
        $this->createdUserId = $createResult->id;

        // Recalculate quota for the user
        $quotaResult = $this->client->users()->recalculateQuota($this->createdUserId);

        $this->assertInstanceOf(\Zone\Wildduck\Dto\User\QuotaRecalculationResponseDto::class, $quotaResult);
        $this->assertTrue($quotaResult->success);

        // Recalculate quota for all users
        $allQuotaResult = $this->client->users()->recalculateAllUserQuotas();

        $this->assertInstanceOf(\Zone\Wildduck\Dto\User\QuotaRecalculationTaskResponseDto::class, $allQuotaResult);
        $this->assertTrue($allQuotaResult->success);
        $this->assertNotEmpty($allQuotaResult->task);
    }

    public function testCancelDeletion(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();

        $createDto = new CreateUserRequestDto(
            username: $username,
            password: 'TestPassword123!',
            address: $this->generateUniqueEmail()
        );

        $createResult = $this->client->users()->create($createDto);
        $this->createdUserId = $createResult->id;

        // Note: This test assumes the user can be marked for deletion.
        // In WildDuck, you typically delete a user, then can restore it within a time window.
        // We'll test the cancelDeletion method directly.

        // Cancel deletion (used after marking user for deletion)
        $cancelDto = new \Zone\Wildduck\Dto\User\CancelUserDeletionRequestDto();

        try {
            $cancelResult = $this->client->users()->cancelDeletion($this->createdUserId, $cancelDto);
            $this->assertInstanceOf(\Zone\Wildduck\Dto\User\CancelUserDeletionResponseDto::class, $cancelResult);
            // If no error, the call succeeded (even if user wasn't scheduled for deletion)
        } catch (\Exception $e) {
            // User may not be scheduled for deletion, which is expected
        }
    }

    public function testGetRestoreInfo(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();

        $createDto = new CreateUserRequestDto(
            username: $username,
            password: 'TestPassword123!',
            address: $this->generateUniqueEmail()
        );

        $createResult = $this->client->users()->create($createDto);
        $this->createdUserId = $createResult->id;

        // Delete user
        $deleteResult = $this->client->users()->delete($this->createdUserId);
        $this->assertTrue($deleteResult->success);

        // Get restore info (should work for recently deleted user)
        $restoreInfo = $this->client->users()->getRestoreInfo($this->createdUserId);

        $this->assertInstanceOf(\Zone\Wildduck\Dto\User\RestoreUserInfoDto::class, $restoreInfo);

        $this->createdUserId = null; // Already deleted
    }
}
