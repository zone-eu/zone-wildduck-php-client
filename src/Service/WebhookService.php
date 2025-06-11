<?php

namespace Zone\Wildduck\Service;

use Override;
use Zone\Wildduck\Collection2;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\Resource\Webhook;
use Zone\Wildduck\Service\Traits\RequiresGlobalToken;

class WebhookService extends AbstractService
{
    use RequiresGlobalToken;

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
        return $this->requestCollection('get', '/webhooks', $params, $opts);
    }

	/**
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Webhook
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function create(array|null $params = null, array|null $opts = null): Webhook
    {
        return $this->request('post', '/webhooks', $params, $opts);
    }

	/**
	 * @param string $id
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Webhook
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function delete(string $id, array|null $params = null, array|null $opts = null): Webhook
    {
        return $this->request('delete', $this->buildPath('/webhooks/%s', $id), $params, $opts);
    }

	#[Override]
	protected function getObjectName(): string
	{
		return Webhook::OBJECT_NAME;
	}
}
