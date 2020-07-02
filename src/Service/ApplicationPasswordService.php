<?php

namespace Zone\Wildduck\Service;

class ApplicationPasswordService extends AbstractService
{

    /**
     * @return \Zone\Wildduck\ApplicationPassword
     */
    public function create(string $user, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/users/%s/asps', $user), $params, $opts);
    }

    public function delete(string $user, string $asp, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/users/%s/asps/%s', $user, $asp), $params, $opts);
    }

    /**
     * @return \Zone\Wildduck\Collection|\Zone\Wildduck\ApplicationPassword[]
     */
    public function all(string $user, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/users/%s/asps', $user), $params, $opts);
    }

    /**
     * @return \Zone\Wildduck\ApplicationPassword
     */
    public function get(string $user, string $asp, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/users/%s/asps/%s', $user, $asp), $params, $opts);
    }
}
