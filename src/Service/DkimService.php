<?php

namespace Zone\Wildduck\Service;

use Override;
use Zone\Wildduck\Resource\Dkim;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\Collection2;

class DkimService extends AbstractService
{
	/**
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Dkim
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function create(array|null $params = null, array|null $opts = null): Dkim
    {
        return $this->request('post', '/dkim', $params, $opts);
    }

	/**
	 * @param string $dkim
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Dkim
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function delete(string $dkim, array|null $params = null, array|null $opts = null): Dkim
    {
        return $this->request('delete', $this->buildPath('/dkim/%s', $dkim), $params, $opts);
    }

	/**
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
	public function all(array|null $params = null, array|null $opts = null): Collection2
    {
        return $this->requestCollection('get', '/dkim', $params, $opts);
    }

	/**
	 * @param string $dkim
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Dkim
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function get(string $dkim, array|null $params = null, array|null $opts = null): Dkim
    {
        return $this->request('get', $this->buildPath('/dkim/%s', $dkim), $params, $opts);
    }

	/**
	 * @param string $domain
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Dkim
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function resolve(string $domain, array|null $params = null, array|null $opts = null): Dkim
    {
        return $this->request('get', $this->buildPath('/dkim/resolve/%s', $domain), $params, $opts);
    }

	#[Override]
	protected function getObjectName(): string
	{
		return Dkim::OBJECT_NAME;
	}
}
