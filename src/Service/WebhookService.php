<?php

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Collection;
use Zone\Wildduck\Service\Traits\RequiresGlobalToken;
use Zone\Wildduck\Webhook;
use Zone\Wildduck\WildduckObject;

class WebhookService extends AbstractService
{
    use RequiresGlobalToken;
    /**
     * @return Collection|Webhook[]
     */
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/webhooks', $params, $opts);
    }

    /**
     * @param null $params
     * @param null $opts
     * @return WildduckObject
     */
    public function create($params = null, $opts = null)
    {
        return $this->request('post', '/webhooks', $params, $opts);
    }

    public function delete(string $id, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/webhook/%s', $id), $params, $opts);
    }

}