<?php

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\Resource\Address;
use Zone\Wildduck\Collection2;
use Zone\Wildduck\Resource\ForwardedAddress;
use Zone\Wildduck\WildduckObject;

class AddressService extends AbstractService
{
	/**
	 * @param string $user
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Address
	 *
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function create(string $user, array|null $params = null, array|null $opts = null): Address
    {
        return $this->request('post', $this->buildPath('/users/%s/addresses', $user), $params, $opts);
    }

	/**
	 * @param array|null $params
	 * @param array|null $opts
	 * @return ForwardedAddress
	 *
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function createForwarded(array|null $params = null, array|null $opts = null): ForwardedAddress
    {
        $opts['object'] = ForwardedAddress::OBJECT_NAME;
        return $this->request('post', '/addresses/forwarded', $params, $opts);
    }

	/**
	 * @param string $user
	 * @param string $address
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Address
	 *
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function delete(string $user, string $address, array|null $params = null, array|null $opts = null): Address
    {
        return $this->request('delete', $this->buildPath('/users/%s/addresses/%s', $user, $address), $params, $opts);
    }

	/**
	 * @param string $address
	 * @param array|null $params
	 * @param array|null $opts
	 * @return ForwardedAddress
	 *
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function deleteForwarded(string $address, array|null $params = null, array|null $opts = null): ForwardedAddress
    {
        $opts['object'] = ForwardedAddress::OBJECT_NAME;
        return $this->request('delete', $this->buildPath('/addresses/forwarded/%s', $address), $params, $opts);
    }

	/**
	 * @param string $address
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Address
	 *
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function resolve(string $address, array|null $params = null, array|null $opts = null): Address
    {
        return $this->request('get', $this->buildPath('/addresses/resolve/%s', $address), $params, $opts);
    }

	/**
	 * @param string $user
	 * @param array|null $params
	 * @param array|null $opts
	 *
	 * @return Collection2|Address[]
	 */
    public function list(string $user, array|null $params = null, array|null $opts = null): Collection2|Address
    {
        return $this->requestCollection('get', $this->buildPath('/users/%s/addresses', $user), $params, $opts);
    }

	/**
	 * @param array|null $params
	 * @param array|null $opts
	 *
	 * @return Collection2|Address[]
	 */
    public function all(array|null $params = null, array|null $opts = null): Collection2|Address
    {
        return $this->requestCollection('get', '/addresses', $params, $opts);
    }

	/**
	 * @param array|null $params
	 * @param array|null $opts
	 *
	 * @return Address
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function renameDomain(array|null $params = null, array|null $opts = null): Address
    {
        return $this->request('put', '/addresses/renameDomain', $params, $opts);
    }

	/**
	 * @param string $user
	 * @param string $address
	 * @param array|null $params
	 * @param array|null $opts
	 *
	 * @return Address
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function get(string $user, string $address, array|null $params = null, array|null $opts = null): Address
    {
        return $this->request('get', $this->buildPath('/users/%s/addresses/%s', $user, $address), $params, $opts);
    }

	/**
	 * @param string $address
	 * @param array|null $params
	 * @param array|null $opts
	 *
	 * @return ForwardedAddress
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function getForwarded(string $address, array|null $params = null, array|null $opts = null): ForwardedAddress
    {
        $opts['object'] = ForwardedAddress::OBJECT_NAME;
        return $this->request('get', $this->buildPath('/addresses/forwarded/%s', $address), $params, $opts);
    }

	/**
	 * @param string $user
	 * @param string $address
	 * @param array|null $params
	 * @param array|null $opts
	 *
	 * @return Address
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function update(string $user, string $address, array|null $params = null, array|null $opts = null): Address
    {
        return $this->request('put', $this->buildPath('/users/%s/addresses/%s', $user, $address), $params, $opts);
    }

	/**
	 * @param string $address
	 * @param array|null $params
	 * @param array|null $opts
	 *
	 * @return ForwardedAddress
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function updateForwarded(string $address, array|null $params = null, array|null $opts = null): ForwardedAddress
    {
        $opts['object'] = ForwardedAddress::OBJECT_NAME;
        return $this->request('put', $this->buildPath('/addresses/forwarded/%s', $address), $params, $opts);
    }

	/**
	 * @param string $user
	 * @param array|null $params
	 * @param array|null $opts
	 *
	 * @return Collection2
	 */
	public function listAddressRegister(string $user, array|null $params = null, array|null $opts = null): Collection2
    {
        return $this->requestCollection('get', $this->buildPath('/users/%s/addressregister', $user), $params, $opts);
    }

	/**
	 * @param string $user
	 * @param string $address
	 * @param array|null $params
	 * @param array|null $opts
	 *
	 * @return WildduckObject
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function updateAddressFromRegister(string $user, string $address, array|null $params = null, array|null $opts = null): WildduckObject
    {
        return $this->request('put', $this->buildPath('/users/%s/addressregister/%s', $user, $address), $params, $opts);
    }
}
