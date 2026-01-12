<?php

declare(strict_types=1);

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\Address\AddressRegisterRequestDto;
use Zone\Wildduck\Dto\Address\AddressResponseDto;
use Zone\Wildduck\Dto\Address\AddressRegisterResponseDto;
use Zone\Wildduck\Dto\Address\AllAddressResponseDto;
use Zone\Wildduck\Dto\Address\CreateAddressRequestDto;
use Zone\Wildduck\Dto\Address\CreateForwardedAddressRequestDto;
use Zone\Wildduck\Dto\Address\ForwardedAddressResponseDto;
use Zone\Wildduck\Dto\Address\ListRegisteredAddressesRequest;
use Zone\Wildduck\Dto\Address\ListAllRegisteredAddressesRequestDto;
use Zone\Wildduck\Dto\Address\ListUserRegisteredAddressesRequestDto;
use Zone\Wildduck\Dto\Address\RenameDomainRequestDto;
use Zone\Wildduck\Dto\Address\RenameDomainResponseDto;
use Zone\Wildduck\Dto\Address\ResolveAddressRequestDto;
use Zone\Wildduck\Dto\Address\ResolvedAddressResponseDto;
use Zone\Wildduck\Dto\Address\UpdateAddressRequestDto;
use Zone\Wildduck\Dto\Address\UpdateAddressRegisterRequestDto;
use Zone\Wildduck\Dto\Address\UpdateForwardedAddressRequestDto;
use Zone\Wildduck\Dto\Shared\CreatedResourceResponseDto;
use Zone\Wildduck\Dto\PaginatedResultDto;
use Zone\Wildduck\Dto\Shared\SuccessResponseDto;
use Zone\Wildduck\Dto\User\UserInfoResponseDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

/**
 * Address service for managing user addresses and forwarded addresses
 */
class AddressService extends AbstractService
{
    /**
     * Create a new address for a user
     *
     * @param string $user
     * @param CreateAddressRequestDto $params
     * @param array|null $opts
     * @return CreatedResourceResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function create(string $user, CreateAddressRequestDto $params, array|null $opts = null): CreatedResourceResponseDto
    {
        return $this->requestDto('post', $this->buildPath('/users/%s/addresses', $user), $params, CreatedResourceResponseDto::class, $opts);
    }

    /**
     * Create a new forwarded address
     *
     * @param CreateForwardedAddressRequestDto $params
     * @param array|null $opts
     * @return CreatedResourceResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function createForwarded(CreateForwardedAddressRequestDto $params, array|null $opts = null): CreatedResourceResponseDto
    {
        return $this->requestDto('post', '/addresses/forwarded', $params, CreatedResourceResponseDto::class, $opts);
    }

    /**
     * Delete an address
     *
     * @param string $user
     * @param string $address
     * @param array|null $opts
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function delete(string $user, string $address, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('delete', $this->buildPath('/users/%s/addresses/%s', $user, $address), null, SuccessResponseDto::class, $opts);
    }

    /**
     * Delete a forwarded address
     *
     * @param string $address
     * @param array|null $opts
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function deleteForwarded(string $address, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('delete', $this->buildPath('/addresses/forwarded/%s', $address), null, SuccessResponseDto::class, $opts);
    }

    /**
     * Resolve an address
     *
     * @param string $address
     * @param ResolveAddressRequestDto $params
     * @param array|null $opts
     * @return ResolvedAddressResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function resolve(string $address, ResolveAddressRequestDto $params, array|null $opts = null): ResolvedAddressResponseDto
    {
        return $this->requestDto('get', $this->buildPath('/addresses/resolve/%s', $address), $params, ResolvedAddressResponseDto::class, $opts);
    }

    /**
     * List addresses for a user
     *
     * @param string $user
     * @param ListUserRegisteredAddressesRequestDto $params
     * @param array|null $opts
     * @return PaginatedResultDto<AddressResponseDto>
     */
    public function list(string $user, ListUserRegisteredAddressesRequestDto $params, array|null $opts = null): PaginatedResultDto
    {
        return $this->requestPaginatedDto('get', $this->buildPath('/users/%s/addresses', $user), $params, AddressResponseDto::class, $opts);
    }

    /**
     * List all registered addresses
     *
     * @param ListAllRegisteredAddressesRequestDto $params
     * @param array|null $opts
     * @return PaginatedResultDto<AllAddressResponseDto>
     */
    public function listAll(ListAllRegisteredAddressesRequestDto $params, array|null $opts = null): PaginatedResultDto
    {
        return $this->requestPaginatedDto('get', '/addresses', $params, AllAddressResponseDto::class, $opts);
    }

    /**
     * Rename domain for all addresses
     *
     * @param RenameDomainRequestDto $params
     * @param array|null $opts
     * @return RenameDomainResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function renameDomain(RenameDomainRequestDto $params, array|null $opts = null): RenameDomainResponseDto
    {
        return $this->requestDto('put', '/addresses/renameDomain', $params, RenameDomainResponseDto::class, $opts);
    }

    /**
     * Get address information
     *
     * @param string $user
     * @param string $address
     * @param array|null $opts
     * @return AddressResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function get(string $user, string $address, array|null $opts = null): AddressResponseDto
    {
        return $this->requestDto('get', $this->buildPath('/users/%s/addresses/%s', $user, $address), null, AddressResponseDto::class, $opts);
    }

    /**
     * Get forwarded address information
     *
     * @param string $address
     * @param array|null $opts
     * @return ForwardedAddressResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function getForwarded(string $address, array|null $opts = null): ForwardedAddressResponseDto
    {
        return $this->requestDto('get', $this->buildPath('/addresses/forwarded/%s', $address), null, ForwardedAddressResponseDto::class, $opts);
    }

    /**
     * Update an address
     *
     * @param string $user
     * @param string $addressId
     * @param UpdateAddressRequestDto $params
     * @param array|null $opts
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function update(string $user, string $addressId, UpdateAddressRequestDto $params, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('put', $this->buildPath('/users/%s/addresses/%s', $user, $addressId), $params, SuccessResponseDto::class, $opts);
    }

    /**
     * Update a forwarded address
     *
     * @param string $address
     * @param UpdateForwardedAddressRequestDto $params
     * @param array|null $opts
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function updateForwarded(string $address, UpdateForwardedAddressRequestDto $params, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('put', $this->buildPath('/addresses/forwarded/%s', $address), $params, SuccessResponseDto::class, $opts);
    }

    /**
     * List address register for a user
     *
     * @param string $user
     * @param AddressRegisterRequestDto $params
     * @param array|null $opts
     * @return PaginatedResultDto<AddressRegisterResponseDto>
     */
    public function listAddressRegister(string $user, AddressRegisterRequestDto $params, array|null $opts = null): PaginatedResultDto
    {
        return $this->requestPaginatedDto('get', $this->buildPath('/users/%s/addressregister', $user), $params, AddressRegisterResponseDto::class, $opts);
    }

    /**
     * Update address from register
     *
     * @param string $user
     * @param string $addressId
     * @param UpdateAddressRegisterRequestDto $params
     * @param array|null $opts
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function updateAddressFromRegister(string $user, string $addressId, UpdateAddressRegisterRequestDto $params, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('put', $this->buildPath('/users/%s/addressregister/%s', $user, $addressId), $params, SuccessResponseDto::class, $opts);
    }
}
