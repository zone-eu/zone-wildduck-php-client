<?php

namespace Zone\Wildduck\Service;

class DkimService extends AbstractService
{

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/dkim', $params, $opts);
    }

    public function delete(string $dkim, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/dkim/%s', $dkim), $params, $opts);
    }

    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/dkim', $params, $opts);
    }

    public function get(string $dkim, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/dkim/%s', $dkim), $params, $opts);
    }

    public function resolve(string $domain, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/dkim/resolve/%s', $domain), $params, $opts);
    }
}