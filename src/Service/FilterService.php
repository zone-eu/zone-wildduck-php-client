<?php

namespace Zone\Wildduck\Service;

use Override;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\Resource\Filter;
use Zone\Wildduck\Collection2;

class FilterService extends AbstractService
{
	/**
	 * @param string $user
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Filter
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function create(string $user, array|null $params = null, array|null $opts = null): Filter
    {
        return $this->request('post', $this->buildPath('/users/%s/filters', $user), $params, $opts);
    }

	/**
	 * @param string $user
	 * @param string $filter
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Filter
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function delete(string $user, string $filter, array|null $params = null, array|null $opts = null): Filter
    {
        return $this->request('delete', $this->buildPath('/users/%s/filters/%s', $user, $filter), $params, $opts);
    }

	/**
	 * @param string $user
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Collection2
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 * @throws InvalidDatabaseException
	 */
	public function all(string $user, array|null $params = null, array|null $opts = null): Collection2
    {
        return $this->requestCollection('get', $this->buildPath('/users/%s/filters', $user), $params, $opts);
    }

	/**
	 * @param string $user
	 * @param string $filter
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Filter
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function get(string $user, string $filter, array|null $params = null, array|null $opts = null): Filter
    {
        return $this->request('get', $this->buildPath('/users/%s/filters/%s', $user, $filter), $params, $opts);
    }

	/**
	 * @param string $user
	 * @param string $filter
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Filter
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function update(string $user, string $filter, array|null $params = null, array|null $opts = null): Filter
    {
        return $this->request('put', $this->buildPath('/users/%s/filters/%s', $user, $filter), $params, $opts);
    }

	/**
	 * @return string
	 */
	#[Override]
	protected function getObjectName(): string
	{
		return Filter::OBJECT_NAME;
	}
}
