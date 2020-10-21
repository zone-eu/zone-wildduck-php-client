<?php

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Message;

class ArchiveService extends AbstractService
{

    public function all(string $user, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/users/%s/archived/messages', $user), $params, $opts);
    }

    public function restore(string $user, string $message, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/users/%s/archived/messages/%s/restore', $user, $message), $params, $opts);
    }

    public function restoreAll(string $user, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/users/%s/archived/restore', $user), $params, $opts);
    }

    protected function getObjectName()
    {
        return Message::OBJECT_NAME;
    }
}