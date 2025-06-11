<?php

namespace Zone\Wildduck\Service;

use Override;
use Zone\Wildduck\Resource\DomainAlias;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\Collection2;

class DomainAliasService extends AbstractService
{
	/**
	 * @param array|null $params
	 * @param array|null $opts
	 * @return DomainAlias
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function create(array|null $params = null, array|null $opts = null): DomainAlias
    {
        return $this->request('post', '/domainaliases', $params, $opts);
    }

	/**
	 * @param string $alias
	 * @param array|null $params
	 * @param array|null $opts
	 * @return DomainAlias
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function delete(string $alias, array|null $params = null, array|null $opts = null): DomainAlias
    {
        return $this->request('delete', $this->buildPath('/domainaliases/%s', $alias), $params, $opts);
    }

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
        return $this->requestCollection('get', '/domainaliases', $params, $opts);
    }

	/**
	 * @param string $alias
	 * @param array|null $params
	 * @param array|null $opts
	 * @return DomainAlias
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function get(string $alias, array|null $params = null, array|null $opts = null): DomainAlias
    {
        return $this->request('get', $this->buildPath('/domainaliases/%s', $alias), $params, $opts);
    }

	/**
	 * @param string $alias
	 * @param array|null $params
	 * @param array|null $opts
	 * @return DomainAlias
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function resolve(string $alias, array|null $params = null, array|null $opts = null): DomainAlias
    {
        return $this->request('get', $this->buildPath('/domainaliases/resolve/%s', $alias), $params, $opts);
    }

	#[Override]
	protected function getObjectName(): string
	{
		return DomainAlias::OBJECT_NAME;
	}
}
