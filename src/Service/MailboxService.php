<?php

namespace Zone\Wildduck\Service;

use Zone\Wildduck\WildduckObject;

class MailboxService extends AbstractService
{

    /**
     * @return \Zone\Wildduck\Mailbox
     */
    public function create($user, $params = null, $opts = null)
    {
        $res = $this->request('post', $this->buildPath('/users/%s/mailboxes', $user), $params, $opts);
        return $this->get($user, $res->id, null, $opts);
    }

    public function delete($user, $mailbox, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/users/%s/mailboxes/%s', $user, $mailbox), $params, $opts);
    }

    public function deleteAllMessages(string $user, string $mailbox, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/users/%s/mailboxes/%s/messages', $user, $mailbox), $params, $opts);
    }

    /**
     * @return \Zone\Wildduck\Collection2
     */
    public function all($user, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/users/%s/mailboxes', $user), $params, $opts);
    }

    /**
     * @return WildduckObject
     */
    public function get($user, $mailbox, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/users/%s/mailboxes/%s', $user, $mailbox), $params, $opts);
    }

    public function update($user, $mailbox, $params = null, $opts = null)
    {
        return $this->request('put', $this->buildPath('/users/%s/mailboxes/%s', $user, $mailbox), $params, $opts);
    }
}