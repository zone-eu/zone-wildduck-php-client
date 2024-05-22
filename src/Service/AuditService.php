<?php

namespace Zone\Wildduck\Service;

use Override;
use Zone\Wildduck\Audit;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

class AuditService extends AbstractService
{
	/**
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Audit
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function create(array|null $params = null, array|null $opts = null): Audit
    {
        return $this->request('post', '/audit', $params, $opts);
    }

	/**
	 * @param string $audit
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Audit
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function export(string $audit, array|null $params = null, array|null $opts = null): Audit
    {
        return $this->request('get', $this->buildPath('/audit/%s/export.mbox', $audit), $params, $opts);
    }

	/**
	 * @param string $audit
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Audit
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function get(string $audit, array|null $params = null, array|null $opts = null): Audit
    {
        return $this->request('get', $this->buildPath('/audit/%s', $audit), $params, $opts);
    }

	#[Override]
	protected function getObjectName(): string
	{
		return Audit::OBJECT_NAME;
	}
}
