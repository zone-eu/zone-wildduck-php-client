<?php

namespace Zone\Wildduck\Service;

use Override;
use Zone\Wildduck\Resource\Autoreply;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;


class AutoreplyService extends AbstractService
{
	/**
	 * @param string $user
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Autoreply
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function delete(string $user, array|null $params = null, array|null $opts = null): Autoreply
    {
        return $this->request('delete', $this->buildPath('/users/%s/autoreply', $user), $params, $opts);
    }

	/**
	 * @param string $user
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Autoreply
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function get(string $user, array|null $params = null, array|null $opts = null): Autoreply
    {
        return $this->request('get', $this->buildPath('/users/%s/autoreply', $user), $params, $opts);
    }

	/**
	 * @param string $user
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Autoreply
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function update(string $user, array|null $params = null, array|null $opts = null): Autoreply
    {
        return $this->request('put', $this->buildPath('/users/%s/autoreply', $user), $params, $opts);
    }

	#[Override]
	protected function getObjectName(): string
	{
		return Autoreply::OBJECT_NAME;
	}
}
