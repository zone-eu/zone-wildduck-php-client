<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\Authentication\AuthenticateRequestDto;
use Zone\Wildduck\Dto\User\CreateUserRequestDto;
use Zone\Wildduck\Dto\User\CreatedUserResponseDto;

class AuthenticationServiceIntegrationTest extends IntegrationTestCase
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

    public function testAuthenticate(): void
    {
        // Create a user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';
        $email = $this->generateUniqueEmail();

        $createUserDto = new CreateUserRequestDto(
            username: $username,
            password: $password,
            address: $email
        );

        $userResult = $this->client->users()->create($createUserDto);
        $this->createdUserId = $userResult->id;

        // Authenticate with username
        $authDto = new AuthenticateRequestDto(
            username: $username,
            password: $password,
            scope: 'master'
        );

        $authResult = $this->client->authentication()->authenticate($authDto);

        $this->assertTrue($authResult->success);
        $this->assertEquals($this->createdUserId, $authResult->id);
        $this->assertEquals($username, $authResult->username);
        $this->assertEquals('master', $authResult->scope);
        $this->assertFalse($authResult->requirePasswordChange);

        // Authenticate with email
        $authDtoEmail = new AuthenticateRequestDto(
            username: $email,
            password: $password,
            scope: 'master'
        );

        $authResultEmail = $this->client->authentication()->authenticate($authDtoEmail);

        $this->assertTrue($authResultEmail->success);
        $this->assertEquals($this->createdUserId, $authResultEmail->id);
    }

    public function testAuthenticationFailure(): void
    {
        // Create a user
        $username = $this->generateUniqueUsername();

        $createUserDto = new CreateUserRequestDto(
            username: $username,
            password: 'TestPassword123!',
            address: $this->generateUniqueEmail()
        );

        $userResult = $this->client->users()->create($createUserDto);
        $this->createdUserId = $userResult->id;

        // Try to authenticate with wrong password - should throw exception
        $authDto = new AuthenticateRequestDto(
            username: $username,
            password: 'WrongPassword123!',
            scope: 'master'
        );

        $this->expectException(\Zone\Wildduck\Exception\AuthenticationFailedException::class);
        $this->client->authentication()->authenticate($authDto);
    }

    public function testInvalidateToken(): void
    {
        // Create a user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';

        $createUserDto = new CreateUserRequestDto(
            username: $username,
            password: $password,
            address: $this->generateUniqueEmail()
        );

        $userResult = $this->client->users()->create($createUserDto);
        $this->createdUserId = $userResult->id;

        // Authenticate to get a token
        $authDto = new AuthenticateRequestDto(
            username: $username,
            password: $password,
            scope: 'master',
            token: true
        );

        $authResult = $this->client->authentication()->authenticate($authDto);
        $this->assertTrue($authResult->success);
        $this->assertNotEmpty($authResult->token);

        // Create a new client with the user's token
        $userClient = new \Zone\Wildduck\WildduckClient([
            'api_base' => self::WILDDUCK_API_URL,
            'access_token' => $authResult->token,
        ]);

        // Invalidate the token
        $invalidateResult = $userClient->authentication()->invalidateToken();

        $this->assertInstanceOf(\Zone\Wildduck\Dto\Shared\SuccessResponseDto::class, $invalidateResult);
        $this->assertTrue($invalidateResult->success);
    }

    public function testPreauth(): void
    {
        // Create a user
        $username = $this->generateUniqueUsername();
        $password = 'TestPassword123!';

        $createUserDto = new CreateUserRequestDto(
            username: $username,
            password: $password,
            address: $this->generateUniqueEmail()
        );

        $userResult = $this->client->users()->create($createUserDto);
        $this->createdUserId = $userResult->id;

        // Generate preauth token
        $preauthDto = new \Zone\Wildduck\Dto\Authentication\PreauthRequestDto(
            username: $username,
            scope: 'master'
        );

        $preauthResult = $this->client->authentication()->preauth($preauthDto);

        $this->assertInstanceOf(\Zone\Wildduck\Dto\Authentication\PreauthResponseDto::class, $preauthResult);
        $this->assertTrue($preauthResult->success);
        $this->assertEquals($username, $preauthResult->username);
    }
}
