<?php

namespace Zone\Wildduck\Service;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Zone\Wildduck\User;

class UserService extends AbstractService
{

    /**
     * @return \Zone\Wildduck\Collection|\Zone\Wildduck\User[]
     */
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/users', $params, $opts);
    }

    public function create($params = null, $opts = null): User
    {
        return $this->request('post', '/users', $params, $opts);
    }

    /**
     * @return \Zone\Wildduck\User
     */
    public function get(\string $id, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/users/%s', $id), $params, $opts);
    }

    public function update(\string $id, $params = null, $opts = null)
    {
        return $this->request('put', $this->buildPath('/users/%s', $id), $params, $opts);
    }

    public function delete(\string $id, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/users/%s', $id), $params, $opts);
    }

    public function logout(\string $id, $params = null, $opts = null)
    {
        return $this->request('put', $this->buildPath('/users/%s/logout', $id), $params, $opts);
    }

    public function updateStream(\string $id, $params = null, $opts = null): StreamedResponse
    {
        return $this->stream('get', $this->buildPath('/users/%s/updates', $id), $params, $opts);
    }

    public function recalculateQuota(string $id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/users/%s/quota/reset', $id), $params, $opts);
    }

    public function recalculateAllUserQuotas($params = null, $opts = null)
    {
        return $this->request('post', '/quota/reset', $params, $opts);
    }

    public function resetPassword(\string $id, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/users/%s/password/reset', $id), $params, $opts);
    }

    public function getIdByUsername(\string $username, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/users/resolve/%s', $username), $params, $opts);
    }
}