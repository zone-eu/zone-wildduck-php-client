<?php

namespace Zone\Wildduck\Service;

class FilterService extends AbstractService
{

    public function create(string $user, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/users/%s/filters', $user), $params, $opts);
    }

    public function delete(string $user, string $filter, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/users/%s/filters/%s', $user, $filter), $params, $opts);
    }

    public function all(string $user, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/users/%s/filters', $user), $params, $opts);
    }

    public function get(string $user, string $filter, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/users/%s/filters/%s', $user, $filter), $params, $opts);
    }

    public function update(string $user, string $filter, $params = null, $opts = null)
    {
        return $this->request('put', $this->buildPath('/users/%s/filters/%s', $user, $filter), $params, $opts);
    }
}