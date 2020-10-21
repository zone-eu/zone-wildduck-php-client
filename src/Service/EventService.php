<?php

namespace Zone\Wildduck\Service;

class EventService extends AbstractService
{

    public function all(string $user, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/users/%s/authlog', $user), $params, $opts);
    }

    public function get(string $user, string $event, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/users/%s/authlog/%s', $user, $event), $params, $opts);
    }
}