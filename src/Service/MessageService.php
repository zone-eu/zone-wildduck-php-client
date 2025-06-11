<?php

namespace Zone\Wildduck\Service;

use Override;
use Zone\Wildduck\ApiResponse;
use Zone\Wildduck\Collection;
use Zone\Wildduck\Collection2;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\Resource\ApiResource;
use Zone\Wildduck\Resource\Attachment;
use Zone\Wildduck\Resource\Message;

class MessageService extends AbstractService
{
	/**
	 * @param string $user
	 * @param string $mailbox
	 * @param int $message
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Message
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function delete(string $user, string $mailbox, int $message, array|null $params = null, array|null $opts = null): Message
    {
        return $this->request(
            'delete',
            $this->buildPath('/users/%s/mailboxes/%s/messages/%s', $user, $mailbox, $message),
            $params,
            $opts
        );
    }

	/**
	 * @param string $user
	 * @param string $queueId
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Message
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function deleteOutbound(string $user, string $queueId, array|null $params = null, array|null $opts = null): Message
    {
        return $this->request('delete', $this->buildPath('/users/%s/outbound/%s', $user, $queueId), $params, $opts);
    }

	/**
	 * @param string $user
	 * @param string $mailbox
	 * @param int $message
	 * @param string $attachment
	 * @param array|null $params
	 * @param array|null $opts
	 * @return string|ApiResponse
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function downloadAttachment(string $user, string $mailbox, int $message, string $attachment, array|null $params = null, array|null $opts = null): string|ApiResponse
	{
        return $this->request(
            'get',
            $this->buildPath(
                '/users/%s/mailboxes/%s/messages/%s/attachments/%s',
                $user,
                $mailbox,
                $message,
                $attachment
            ),
            $params,
            $opts
        );
    }

	/**
	 * @param string $user
	 * @param string $mailbox
	 * @param int $message
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Message
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function forward(string $user, string $mailbox, int $message, array|null $params = null, array|null $opts = null): Message
    {
        return $this->request(
            'post',
            $this->buildPath('/users/%s/mailboxes/%s/messages/%s/forward', $user, $mailbox, $message),
            $params,
            $opts
        );
    }

	/**
	 * @param string $user
	 * @param string $mailbox
	 * @param int $message
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Message
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function events(string $user, string $mailbox, int $message, array|null $params = null, array|null $opts = null): Message
    {
        return $this->request(
            'get',
            $this->buildPath('/users/%s/mailboxes/%s/messages/%s/events', $user, $mailbox, $message),
            $params,
            $opts
        );
    }

	/**
	 * @param string $user
	 * @param string $mailbox
	 * @param int $message
	 * @param array|null $params
	 * @param array|null $opts
	 * @return ApiResponse
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function source(string $user, string $mailbox, int $message, array|null $params = null, array|null $opts = null): ApiResponse
    {
        $opts['raw'] = true;
        return $this->request(
            'get',
            $this->buildPath(
                '/users/%s/mailboxes/%s/messages/%s/message.eml',
                $user,
                $mailbox,
                $message
            ),
            $params,
            $opts
        );
    }

	/**
	 * @param string $user
	 * @param string $mailbox
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Collection2|Message[]
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws InvalidDatabaseException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function all(string $user, string $mailbox, array|null $params = null, array|null $opts = null): Collection2|Message
    {
        return $this->requestCollection(
            'get',
            $this->buildPath('/users/%s/mailboxes/%s/messages', $user, $mailbox),
            $params,
            $opts
        );
    }

	/**
	 * @param string $user
	 * @param string $mailbox
	 * @param int $message
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Message
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function get(string $user, string $mailbox, int $message, array|null $params = null, array|null $opts = null): Message
    {
        return $this->request(
            'get',
            $this->buildPath('/users/%s/mailboxes/%s/messages/%s', $user, $mailbox, $message),
            $params,
            $opts
        );
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
	public function search(string $user, array|null $params = null, array|null $opts = null): Collection2
    {
        return $this->requestCollection('get', $this->buildPath('/users/%s/search', $user), $params, $opts);
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
	 * @link https://docs.wildduck.email/api/#operation/searchApplyMessages
	 */
    public function searchApplyMessages(string $user, array|null $params = null, array|null $opts = null): Message
    {
        return $this->request('post', $this->buildPath('/users/%s/search', $user), $params, $opts);
    }

	/**
	 * @param string $user
	 * @param string $mailbox
	 * @param int $message
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Message
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function submitDraft(string $user, string $mailbox, int $message, array|null $params = null, array|null $opts = null): Message
    {
        return $this->request(
            'post',
            $this->buildPath('/users/%s/mailboxes/%s/messages/%s/submit', $user, $mailbox, $message),
            $params,
            $opts
        );
    }

	/**
	 * @param string $user
	 * @param string $mailbox
	 * @param array|null $params
	 * @param array|null $opts
	 * @return string|Message
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function update(string $user, string $mailbox, array|null $params = null, array|null $opts = null): string|Message
    {
        return $this->request(
            'put',
            $this->buildPath('/users/%s/mailboxes/%s/messages', $user, $mailbox),
            $params,
            $opts
        );
    }

	/**
	 * @param string $user
	 * @param string $mailbox
	 * @param array|null $params
	 * @param array|null $opts
	 * @return Message
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function upload(string $user, string $mailbox, array|null $params = null, array|null $opts = null): Message
    {
        return $this->request(
            'post',
            $this->buildPath('/users/%s/mailboxes/%s/messages', $user, $mailbox),
            $params,
            $opts
        );
    }

	#[Override]
	public function getObjectName(): string
	{
		return Message::OBJECT_NAME;
	}
}
