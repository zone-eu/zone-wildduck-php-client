<?php

namespace Zone\Wildduck\Service;

class AddressService extends AbstractService
{

    public function create(string $user, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/users/%s/addresses', $user), $params, $opts);
    }

    public function createForwarded($params = null, $opts = null)
    {
        return $this->request('post', '/addresses/forwarded', $params, $opts);
    }

    public function delete(string $user, string $address, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/users/%s/addresses/%s', $user, $address), $params, $opts);
    }

    public function deleteForwarded(string $address, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/addresses/forwarded/%s', $address), $params, $opts);
    }

    public function resolve(string $address, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/addresses/resolve/%s', $address), $params, $opts);
    }

    public function list(string $user, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/users/%s/addresses', $user), $params, $opts);
    }

    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/addresses', $params, $opts);
    }

    public function renameDomain($params = null, $opts = null)
    {
        return $this->request('put', '/addresses/renameDomain', $params, $opts);
    }

    public function get(string $user, string $address, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/users/%s/addresses/%s', $user, $address), $params, $opts);
    }

    public function getForwarded(string $address, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/addresses/forwarded/%s', $address), $params, $opts);
    }

    public function update(string $user, string $address, $params = null, $opts = null)
    {
        return $this->request('put', $this->buildPath('/users/%s/addresses/%s', $user, $address), $params, $opts);
    }

    public function updateForwarded(string $address, $params = null, $opts = null)
    {
        return $this->request('put', $this->buildPath('/addresses/forwarded/%s', $address), $params, $opts);
    }
}
