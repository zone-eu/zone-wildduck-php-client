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
use Zone\Wildduck\Resource\Mailbox;

class MailboxService extends AbstractService
{
	/**
	 * @param string $user
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Mailbox
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function create(string $user, array|null $params = null, array|null $opts = null): Mailbox
    {
        $res = $this->request('post', $this->buildPath('/users/%s/mailboxes', $user), $params, $opts);
        return $this->get($user, $res->id, null, $opts);
    }

	/**
	 * @param string $user
	 * @param string $mailbox
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Mailbox
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function delete(string $user, string $mailbox, array|null $params = null, array|null $opts = null): Mailbox
    {
        return $this->request('delete', $this->buildPath('/users/%s/mailboxes/%s', $user, $mailbox), $params, $opts);
    }

	/**
	 * @param string $user
	 * @param string $mailbox
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Mailbox
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function deleteAllMessages(string $user, string $mailbox, array|null $params = null, array|null $opts = null): Mailbox
    {
        return $this->request('delete', $this->buildPath('/users/%s/mailboxes/%s/messages', $user, $mailbox), $params, $opts);
    }

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
        return $this->requestCollection('get', $this->buildPath('/users/%s/mailboxes', $user), $params, $opts);
    }

	/**
	 * @param string $user
	 * @param string $mailbox
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Mailbox
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function get(string $user, string $mailbox, array|null $params = null, array|null $opts = null): Mailbox
    {
        return $this->request('get', $this->buildPath('/users/%s/mailboxes/%s', $user, $mailbox), $params, $opts);
    }

	/**
	 * @param string $user
	 * @param string $mailbox
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Mailbox
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function update(string $user, string $mailbox, array|null $params = null, array|null $opts = null): Mailbox
    {
        return $this->request('put', $this->buildPath('/users/%s/mailboxes/%s', $user, $mailbox), $params, $opts);
    }

	#[Override]
	protected function getObjectName(): string
	{
		return Mailbox::OBJECT_NAME;
	}
}
