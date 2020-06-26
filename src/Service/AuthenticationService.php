<?php

namespace Zone\Wildduck\Service;

class AuthenticationService extends AbstractService
{

    public function authenticate($params = null, $opts = null)
    {
        return $this->request('post', '/authenticate', $params, $opts);
    }

    public function invalidate($params = null, $opts = null)
    {
        return $this->request('delete', '/authenticate', $params, $opts);
    }
}