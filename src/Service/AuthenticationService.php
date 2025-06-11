<?php

namespace Zone\Wildduck\Service;

use Override;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\Resource\User;

class AuthenticationService extends AbstractService
{
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
	public function authenticate(array|null $params = null, array|null $opts = null): User
    {
	    return $this->request('post', '/authenticate', $params, $opts);
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
	public function invalidate(array|null $params = null, array|null $opts = null): User
    {
        return $this->request('delete', '/authenticate', $params, $opts);
    }

	#[Override]
	protected function getObjectName(): string
	{
		return User::OBJECT_NAME;
	}
}
