<?php

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Collection;
use Zone\Wildduck\Collection2;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\Message;
use Zone\Wildduck\WildduckObject;

class MessageService extends AbstractService
{

    public function delete(string $user, string $mailbox, string $message, $params = null, $opts = null)
    {
        return $this->request(
            'delete',
            $this->buildPath('/users/%s/mailboxes/%s/messages/%s', $user, $mailbox, $message),
            $params,
            $opts
        );
    }

    public function deleteOutbound(string $user, string $queueId, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/users/%s/outbound/%s', $user, $queueId), $params, $opts);
    }

    public function downloadAttachment(
        string $user,
        string $mailbox,
        string $message,
        string $attachment,
        $params = null,
        $opts = null
    ) {
//        $opts['raw'] = true;
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

    public function forward(string $user, string $mailbox, string $message, $params = null, $opts = null)
    {
        return $this->request(
            'post',
            $this->buildPath('/users/%s/mailboxes/%s/messages/%s/forward', $user, $mailbox, $message),
            $params,
            $opts
        );
    }

    public function events(string $user, string $mailbox, string $message, $params = null, $opts = null)
    {
        return $this->request(
            'get',
            $this->buildPath('/users/%s/mailboxes/%s/messages/%s/events', $user, $mailbox, $message),
            $params,
            $opts
        );
    }

    public function source(string $user, string $mailbox, string $message, $params = null, $opts = null)
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
     * @return Collection|Message[]
     */
    public function all(string $user, string $mailbox, $params = null, $opts = null)
    {
        return $this->requestCollection(
            'get',
            $this->buildPath('/users/%s/mailboxes/%s/messages', $user, $mailbox),
            $params,
            $opts
        );
    }

    /**
     * @return Message
     */
    public function get(string $user, string $mailbox, string $message, $params = null, $opts = null)
    {
        return $this->request(
            'get',
            $this->buildPath('/users/%s/mailboxes/%s/messages/%s', $user, $mailbox, $message),
            $params,
            $opts
        );
    }

    public function search(string $user, $params = null, $opts = null): Collection2
    {
        return $this->requestCollection('get', $this->buildPath('/users/%s/search', $user), $params, $opts);
    }

    /**
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     *
     * @link https://docs.wildduck.email/api/#operation/searchApplyMessages
     */
    public function searchApplyMessages(string $user, $params = null, $opts = null): WildduckObject
    {
        return $this->request('post', $this->buildPath('/users/%s/search', $user), $params, $opts);
    }

    /**
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function submitDraft(string $user, string $mailbox, string $message, $params = null, $opts = null): WildduckObject
    {
        return $this->request(
            'post',
            $this->buildPath('/users/%s/mailboxes/%s/messages/%s/submit', $user, $mailbox, $message),
            $params,
            $opts
        );
    }

    /**
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function update(string $user, string $mailbox, $params = null, $opts = null)
    {
        return $this->request(
            'put',
            $this->buildPath('/users/%s/mailboxes/%s/messages', $user, $mailbox),
            $params,
            $opts
        );
    }

    public function upload(string $user, string $mailbox, $params = null, $opts = null)
    {
        return $this->request(
            'post',
            $this->buildPath('/users/%s/mailboxes/%s/messages', $user, $mailbox),
            $params,
            $opts
        );
    }
}
