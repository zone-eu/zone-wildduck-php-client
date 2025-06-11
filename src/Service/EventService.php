<?php

namespace Zone\Wildduck\Service;

use Override;
use Zone\Wildduck\Event;
use Zone\Wildduck\Collection2;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

class EventService extends AbstractService
{
	/**
	 * @param string $user
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
	public function all(string $user, array|null $params = null, array|null $opts = null): Collection2
    {
        return $this->requestCollection('get', $this->buildPath('/users/%s/authlog', $user), $params, $opts);
    }

	/**
	 * @param string $user
	 * @param string $event
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Event
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function get(string $user, string $event, array|null $params = null, array|null $opts = null): Event
    {
        return $this->request('get', $this->buildPath('/users/%s/authlog/%s', $user, $event), $params, $opts);
    }

	#[Override]
	protected function getObjectName(): string
	{
		return Event::OBJECT_NAME;
	}
}
