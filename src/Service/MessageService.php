<?php

namespace Zone\Wildduck\Service;

class MessageService extends AbstractService
{

    public function delete(string $user, string $mailbox, string $message, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/users/%s/mailboxes/%s/messages/%s', $user, $mailbox, $message), $params, $opts);
    }

    public function downloadAttachment(string $user, string $mailbox, string $message, string $attachment, $params = null, $opts = null)
    {
        $opts['raw'] = true;
        return $this->request('get', $this->buildPath('/users/%s/mailboxes/%s/messages/%s/attachments/%s', $user, $mailbox, $message, $attachment), $params, $opts);
    }

    public function forward(string $user, string $mailbox, string $message, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/users/%s/mailboxes/%s/messages/%s/forward', $user, $mailbox, $message), $params, $opts);
    }

    public function source(string $user, string $mailbox, string $message, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/users/%s/mailboxes/%s/messages/%s/message.eml', $user, $mailbox, $message), $params, $opts);
    }

    /**
     * @return \Zone\Wildduck\Collection|\Zone\Wildduck\Message[]
     */
    public function all(string $user, string $mailbox, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/users/%s/mailboxes/%s/messages', $user, $mailbox), $params, $opts);
    }

    /**
     * @return \Zone\Wildduck\Message
     */
    public function get(string $user, string $mailbox, string $message, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/users/%s/mailboxes/%s/messages/%s', $user, $mailbox, $message), $params, $opts);
    }

    /**
     * @return \Zone\Wildduck\Collection|\Zone\Wildduck\Message[]
     */
    public function search(string $user, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/users/%s/search', $user), $params, $opts);
    }

    public function submitDraft(string $user, string $mailbox, string $message, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/users/%s/mailboxes/%s/messages/%s/submit', $user, $mailbox, $message), $params, $opts);
    }

    public function update(string $user, string $mailbox, $params = null, $opts = null)
    {
        return $this->request('put', $this->buildPath('/users/%s/mailboxes/%s/messages', $user, $mailbox), $params, $opts);
    }

    public function upload(string $user, string $mailbox, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/users/%s/mailboxes/%s/messages', $user, $mailbox), $params, $opts);
    }
}
