<?php

namespace Zone\Wildduck\Service;

class AutoreplyService extends AbstractService
{

    public function delete(string $user, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/users/%s/autoreply', $user), $params, $opts);
    }

    public function get(string $user, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/users/%s/autoreply', $user), $params, $opts);
    }

    public function update(string $user, $params = null, $opts = null)
    {
        return $this->request('put', $this->buildPath('/users/%s/autoreply', $user), $params, $opts);
    }
}