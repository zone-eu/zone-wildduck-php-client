<?php

namespace Zone\Wildduck\Service;

use Zone\Wildduck\ForwardedAddress;

class AddressService extends AbstractService
{

    /**
     * @return \Zone\Wildduck\Address
     */
    public function create(string $user, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/users/%s/addresses', $user), $params, $opts);
    }

    /**
     * @return \Zone\Wildduck\ForwardedAddress
     */
    public function createForwarded($params = null, $opts = null)
    {
        $opts['object'] = ForwardedAddress::OBJECT_NAME;
        return $this->request('post', '/addresses/forwarded', $params, $opts);
    }

    public function delete(string $user, string $address, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/users/%s/addresses/%s', $user, $address), $params, $opts);
    }

    public function deleteForwarded(string $address, $params = null, $opts = null)
    {
        $opts['object'] = ForwardedAddress::OBJECT_NAME;
        return $this->request('delete', $this->buildPath('/addresses/forwarded/%s', $address), $params, $opts);
    }

    public function resolve(string $address, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/addresses/resolve/%s', $address), $params, $opts);
    }

    /**
     * @return \Zone\Wildduck\Collection|\Zone\Wildduck\Address[]
     */
    public function list(string $user, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/users/%s/addresses', $user), $params, $opts);
    }

    /**
     * @return \Zone\Wildduck\Collection|\Zone\Wildduck\Address[]
     */
    public function all($params = null, $opts = null)
    {
        return $this->requestCollection('get', '/addresses', $params, $opts);
    }

    public function renameDomain($params = null, $opts = null)
    {
        return $this->request('put', '/addresses/renameDomain', $params, $opts);
    }

    /**
     * @return \Zone\Wildduck\Address
     */
    public function get(string $user, string $address, $params = null, $opts = null)
    {
        return $this->request('get', $this->buildPath('/users/%s/addresses/%s', $user, $address), $params, $opts);
    }

    /**
     * @return \Zone\Wildduck\ForwardedAddress
     */
    public function getForwarded(string $address, $params = null, $opts = null)
    {
        $opts['object'] = ForwardedAddress::OBJECT_NAME;
        return $this->request('get', $this->buildPath('/addresses/forwarded/%s', $address), $params, $opts);
    }

    public function update(string $user, string $address, $params = null, $opts = null)
    {
        return $this->request('put', $this->buildPath('/users/%s/addresses/%s', $user, $address), $params, $opts);
    }

    public function updateForwarded(string $address, $params = null, $opts = null)
    {
        $opts['object'] = ForwardedAddress::OBJECT_NAME;
        return $this->request('put', $this->buildPath('/addresses/forwarded/%s', $address), $params, $opts);
    }

    public function listAddressRegister(string $user, $params = null, $opts = null)
    {
        return $this->requestCollection('get', $this->buildPath('/users/%s/addressregister', $user), $params, $opts);
    }
}
