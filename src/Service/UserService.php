<?php

namespace Zone\Wildduck\Service;

use ErrorException;
use Override;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\Collection2;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Zone\Wildduck\Resource\User;

class UserService extends AbstractService
{
	/**
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Collection2
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws InvalidDatabaseException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function all(array|null $params = null, array|null $opts = null): Collection2
    {
        return $this->requestCollection('get', '/users', $params, $opts);
    }

    /**
     * @param array|null $params
     * @param array|null $opts
     * @return User
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function create(array|null $params = null, array|null $opts = null): User
    {
        return $this->request('post', '/users', $params, $opts);
    }

    /**
     * @param string $id
     * @param array|null $params
     * @param array|null $opts
     * @return User
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function get(string $id, array|null $params = null, array|null $opts = null): User
    {
		return $this->request('get', $this->buildPath('/users/%s', $id), $params, $opts);
    }

    /**
     * @param string $id
     * @param array|null $params
     * @param array|null $opts
     * @return User
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function update(string $id, array|null $params = null, array|null $opts = null): User
    {
        return $this->request('put', $this->buildPath('/users/%s', $id), $params, $opts);
    }

    /**
     * @param string $id
     * @param array|null $params
     * @param array|null $opts
     * @return User
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function delete(string $id, array|null $params = null, array|null $opts = null): User
    {
        return $this->request('delete', $this->buildPath('/users/%s', $id), $params, $opts);
    }

    /**
     * @param string $id
     * @param array|null $params
     * @param array|null $opts
     * @return User
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function logout(string $id, array|null $params = null, array|null $opts = null): User
    {
        return $this->request('put', $this->buildPath('/users/%s/logout', $id), $params, $opts);
    }

	/**
	 * @param string $id
	 * @param array|null $params
	 * @param array|null $opts
	 * @return StreamedResponse
	 * @throws ErrorException
	 */
    public function updateStream(string $id, array|null $params = null, array|null $opts = null): StreamedResponse
    {
        return $this->stream('get', $this->buildPath('/users/%s/updates', $id), $params, $opts);
    }

	/**
	 * @param string $id
	 * @param array|null $params
	 * @param array|null $opts
	 * @return User
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function recalculateQuota(string $id, array|null $params = null, array|null $opts = null): User
    {
        return $this->request('post', $this->buildPath('/users/%s/quota/reset', $id), $params, $opts);
    }

	/**
	 * @param array|null $params
	 * @param array|null $opts
	 * @return User
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function recalculateAllUserQuotas(array|null $params = null, array|null $opts = null): User
    {
        return $this->request('post', '/quota/reset', $params, $opts);
    }

	/**
	 * @param string $id
	 * @param array|null $params
	 * @param array|null $opts
	 * @return User
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function resetPassword(string $id, array|null $params = null, array|null $opts = null): User
    {
        return $this->request('post', $this->buildPath('/users/%s/password/reset', $id), $params, $opts);
    }

	/**
	 * @param string $username
	 * @param array|null $params
	 * @param array|null $opts
	 * @return User
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function getIdByUsername(string $username, array|null $params = null, array|null $opts = null): User
    {
        return $this->request('get', $this->buildPath('/users/resolve/%s', $username), $params, $opts);
    }

	#[Override]
	protected function getObjectName(): string
	{
		return User::OBJECT_NAME;
	}
}
