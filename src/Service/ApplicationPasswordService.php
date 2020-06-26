<?php

namespace Zone\Wildduck\Service;

class ApplicationPasswordService extends AbstractService
{

    public function create(string $user, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/users/%s/asps', $user), $params, $opts);
    }

    public function delete(string $user, string $asp, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/users/%s/asps/%s', $user, $asp), $params, $opts);
    }

    public function all(string $user, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/users/%s/asps', $user), $params, $opts);
    }

    public function get(string $user, string $asp, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/users/%s/asps/%s', $user, $asp), $params, $opts);
    }
}
