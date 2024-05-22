<?php

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;
use Override;
use Zone\Wildduck\Collection2;
use Zone\Wildduck\Resource\Message;

class ArchiveService extends AbstractService
{
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
		return $this->requestCollection('get', $this->buildPath('/users/%s/archived/messages', $user), $params, $opts);
    }

	/**
	 * @param string $user
	 * @param string $message
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Message
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function restore(string $user, string $message, array|null $params = null, array|null $opts = null): Message
    {
        return $this->request('post', $this->buildPath('/users/%s/archived/messages/%s/restore', $user, $message), $params, $opts);
    }

	/**
	 * @param string $user
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Message
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function restoreAll(string $user, array|null $params = null, array|null $opts = null): Message
    {
        return $this->request('post', $this->buildPath('/users/%s/archived/restore', $user), $params, $opts);
    }

    #[Override]
    protected function getObjectName(): string
    {
        return Message::OBJECT_NAME;
    }
}
