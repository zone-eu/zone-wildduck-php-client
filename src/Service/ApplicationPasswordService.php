<?php

namespace Zone\Wildduck\Service;

use Override;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\Resource\ApplicationPassword;
use Zone\Wildduck\Collection2;

class ApplicationPasswordService extends AbstractService
{
	/**
	 * @param string $user
	 * @param array|null $params
	 * @param array|null $opts
	 * @return ApplicationPassword
	 *
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function create(string $user, array|null $params = null, array|null $opts = null): ApplicationPassword
    {
        return $this->request('post', $this->buildPath('/users/%s/asps', $user), $params, $opts);
    }

	/**
	 * @param string $user
	 * @param string $asp
	 * @param array|null $params
	 * @param array|null $opts
	 *
	 * @return ApplicationPassword
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function delete(string $user, string $asp, array|null $params = null, array|null $opts = null): ApplicationPassword
    {
        return $this->request('delete', $this->buildPath('/users/%s/asps/%s', $user, $asp), $params, $opts);
    }

	/**
	 * @param string $user
	 * @param array|null $params
	 * @param array|null $opts
	 *
	 * @return Collection2|ApplicationPassword[]
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws InvalidDatabaseException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function all(string $user, array|null  $params = null, array|null $opts = null): Collection2|ApplicationPassword
    {
        return $this->requestCollection('get', $this->buildPath('/users/%s/asps', $user), $params, $opts);
    }

	/**
	 * @param string $user
	 * @param string $asp
	 * @param array|null $params
	 * @param array|null $opts
	 *
	 * @return ApplicationPassword
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function get(string $user, string $asp, array|null $params = null, array|null $opts = null): ApplicationPassword
    {
        return $this->request('get', $this->buildPath('/users/%s/asps/%s', $user, $asp), $params, $opts);
    }

	#[Override]
	protected function getObjectName(): string
	{
		return ApplicationPassword::OBJECT_NAME;
	}
}
