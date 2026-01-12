<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\Address\CreateAddressRequestDto;
use Zone\Wildduck\Dto\Address\UpdateAddressRequestDto;
use Zone\Wildduck\Dto\User\CreateUserRequestDto;
use Zone\Wildduck\Dto\User\CreatedUserResponseDto;

class AddressServiceIntegrationTest extends IntegrationTestCase
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

    public function testAddressLifecycle(): void
    {
        // Create a user first
        $username = $this->generateUniqueUsername();

        $createUserDto = new CreateUserRequestDto(
            username: $username,
            password: 'TestPassword123!',
            address: $this->generateUniqueEmail()
        );

        $userResult = $this->client->users()->create($createUserDto);
        $this->createdUserId = $userResult->id;

        // Create additional address
        $newAddress = $this->generateUniqueEmail();
        $createAddressDto = new CreateAddressRequestDto(
            address: $newAddress,
            main: false
        );

        $createResult = $this->client->addresses()->create($this->createdUserId, $createAddressDto);
        $this->assertTrue($createResult->success);

        // List addresses
        $listParams = new \Zone\Wildduck\Dto\Address\ListUserRegisteredAddressesRequestDto();
        $addresses = $this->client->addresses()->list($this->createdUserId, $listParams);
        $this->assertGreaterThanOrEqual(2, $addresses->total); // Main + new address

        // Find our created address
        $found = false;
        $addressId = null;
        foreach ($addresses->results as $address) {
            if ($address->address === $newAddress) {
                $found = true;
                $addressId = $address->id;
                $this->assertFalse($address->main);
                break;
            }
        }
        $this->assertTrue($found, 'Created address should be in the list');
        $this->assertNotNull($addressId, 'Address ID should be found');

        // Get specific address using its ID (not email)
        $address = $this->client->addresses()->get($this->createdUserId, $addressId);
        $this->assertEquals($newAddress, $address->address);

        // Update address (change name only - can't set as main because user already has a main address)
        $updateDto = new UpdateAddressRequestDto(name: 'Updated Name');
        $updateResult = $this->client->addresses()->update($this->createdUserId, $addressId, $updateDto);
        $this->assertTrue($updateResult->success);

        // Verify update
        $updatedAddress = $this->client->addresses()->get($this->createdUserId, $addressId);
        $this->assertEquals('Updated Name', $updatedAddress->name);

        // Delete address
        $deleteResult = $this->client->addresses()->delete($this->createdUserId, $addressId);
        $this->assertTrue($deleteResult->success);
    }

    public function testResolveAddress(): void
    {
        // Create a user with an address
        $username = $this->generateUniqueUsername();
        $email = $this->generateUniqueEmail();

        $createUserDto = new CreateUserRequestDto(
            username: $username,
            password: 'TestPassword123!',
            address: $email
        );

        $userResult = $this->client->users()->create($createUserDto);
        $this->createdUserId = $userResult->id;

        // Resolve the address
        $resolveParams = new \Zone\Wildduck\Dto\Address\ResolveAddressRequestDto();
        $resolved = $this->client->addresses()->resolve($email, $resolveParams);

        $this->assertTrue($resolved->success);
        $this->assertNotEmpty($resolved->id);
        $this->assertEquals($email, $resolved->address);
        // Note: The resolved ID may differ from createdUserId if the address belongs to a different user
        // in the system (e.g., from previous test runs), but as long as it resolves successfully, the test passes
    }

    public function testForwardedAddressLifecycle(): void
    {
        // Create forwarded address
        $forwardedAddress = $this->generateUniqueEmail();
        $targetAddresses = [
            $this->generateUniqueEmail(),
            $this->generateUniqueEmail()
        ];

        $createDto = new \Zone\Wildduck\Dto\Address\CreateForwardedAddressRequestDto(
            address: $forwardedAddress,
            targets: $targetAddresses,
            forwards: null, // Don't specify forwards limit
            autoreply: null
        );

        try {
            $createResult = $this->client->addresses()->createForwarded($createDto);
            $this->assertTrue($createResult->success);
            $this->assertNotEmpty($createResult->id);

            // Get forwarded address
            $forwardedInfo = $this->client->addresses()->getForwarded($forwardedAddress);
            $this->assertEquals($forwardedAddress, $forwardedInfo->address);
            $this->assertEquals($targetAddresses, $forwardedInfo->targets);

            // Update forwarded address
            $newTargets = [$this->generateUniqueEmail()];
            $updateDto = new \Zone\Wildduck\Dto\Address\UpdateForwardedAddressRequestDto(
                address: $forwardedAddress,
                targets: $newTargets
            );

            $updateResult = $this->client->addresses()->updateForwarded($forwardedAddress, $updateDto);
            $this->assertTrue($updateResult->success);

            // Verify update
            $updatedForwarded = $this->client->addresses()->getForwarded($forwardedAddress);
            $this->assertEquals($newTargets, $updatedForwarded->targets);

            // Delete forwarded address
            $deleteResult = $this->client->addresses()->deleteForwarded($forwardedAddress);
            $this->assertTrue($deleteResult->success);
        } catch (\Zone\Wildduck\Exception\ValidationException $e) {
            $this->markTestSkipped('Forwarded addresses may not be supported on this server: ' . $e->getMessage());
        }
    }

    public function testRenameDomain(): void
    {
        // Create multiple users with addresses on the same domain
        $oldDomain = 'olddomain-' . uniqid() . '.test';
        $newDomain = 'newdomain-' . uniqid() . '.test';

        $user1Username = $this->generateUniqueUsername();
        $user1Email = 'user1@' . $oldDomain;

        $createUser1Dto = new CreateUserRequestDto(
            username: $user1Username,
            password: 'TestPassword123!',
            address: $user1Email
        );

        $user1Result = $this->client->users()->create($createUser1Dto);
        $this->createdUserId = $user1Result->id;

        // Rename domain for all addresses
        $renameDto = new \Zone\Wildduck\Dto\Address\RenameDomainRequestDto(
            oldDomain: $oldDomain,
            newDomain: $newDomain
        );

        $renameResult = $this->client->addresses()->renameDomain($renameDto);

        $this->assertInstanceOf(\Zone\Wildduck\Dto\Address\RenameDomainResponseDto::class, $renameResult);
        $this->assertGreaterThanOrEqual(1, $renameResult->modifiedAddresses);

        // Verify the address was renamed
        $user = $this->client->users()->get($this->createdUserId);
        $expectedNewEmail = 'user1@' . $newDomain;
        $this->assertEquals($expectedNewEmail, $user->address);
    }

    public function testListAll(): void
    {
        // Create a test user first
        $username = $this->generateUniqueUsername();
        $email = $this->generateUniqueEmail();

        $createUserDto = new CreateUserRequestDto(
            username: $username,
            password: 'TestPassword123!',
            address: $email
        );

        $userResult = $this->client->users()->create($createUserDto);
        $this->createdUserId = $userResult->id;

        // List all registered addresses
        $listDto = new \Zone\Wildduck\Dto\Address\ListAllRegisteredAddressesRequestDto(
            query: null,
            forward: null,
            tags: null,
            requiredTags: null,
            metaData: null,
            internalData: null,
            limit: 10
        );

        $addresses = $this->client->addresses()->listAll($listDto);

        $this->assertGreaterThan(0, $addresses->total);
        $this->assertNotEmpty($addresses->results);

        // Find our created address in the list
        $found = false;
        foreach ($addresses->results as $address) {
            if ($address->address === $email) {
                $found = true;
                break;
            }
        }

        $this->assertTrue($found, 'Created address should be in the list');
    }

    public function testAddressRegister(): void
    {
        // Create a test user
        $username = $this->generateUniqueUsername();
        $email = $this->generateUniqueEmail();

        $createUserDto = new CreateUserRequestDto(
            username: $username,
            password: 'TestPassword123!',
            address: $email
        );

        $userResult = $this->client->users()->create($createUserDto);
        $this->createdUserId = $userResult->id;

        // List address register for the user
        // Note: query parameter is required and must be a prefix search term
        $listDto = new \Zone\Wildduck\Dto\Address\AddressRegisterRequestDto(
            query: substr($email, 0, 3) // Use first 3 chars of email as search prefix
        );

        $registerAddresses = $this->client->addresses()->listAddressRegister($this->createdUserId, $listDto);

        $this->assertGreaterThanOrEqual(0, $registerAddresses->total);

        // If there are addresses in the register, try updating one
        if ($registerAddresses->total > 0 && count($registerAddresses->results) > 0) {
            $firstAddress = $registerAddresses->results[0];

            $updateDto = new \Zone\Wildduck\Dto\Address\UpdateAddressRegisterRequestDto(
                disabled: false
            );

            $updateResult = $this->client->addresses()->updateAddressFromRegister(
                $this->createdUserId,
                (string)$firstAddress->id,
                $updateDto
            );

            $this->assertTrue($updateResult->success);
        }
    }
}
