<?php

namespace Zone\Wildduck\Service;

class AuditService extends AbstractService
{

    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/audit', $params, $opts);
    }

    public function export(string $audit, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/audit/%s/export.mbox', $audit), $params, $opts);
    }

    public function get(string $audit, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/audit/%s', $audit), $params, $opts);
    }
}