<?php

namespace Zone\Wildduck\Service;

class DomainAliasService extends AbstractService
{

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/domainaliases', $params, $opts);
    }

    public function delete(string $alias, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/domainaliases/%s', $alias), $params, $opts);
    }

    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/domainaliases', $params, $opts);
    }

    public function get(string $alias, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/domainaliases/%s', $alias), $params, $opts);
    }

    public function resolve(string $alias, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/domainaliases/resolve/%s', $alias), $params, $opts);
    }
}