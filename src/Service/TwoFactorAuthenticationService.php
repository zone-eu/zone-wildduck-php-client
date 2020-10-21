<?php

namespace Zone\Wildduck\Service;

class TwoFactorAuthenticationService extends AbstractService
{

    public function disable(string $user, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/users/%s/2fa', $user), $params, $opts);
    }

    public function disableTOTPAuth(string $user, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/users/%s/2fa/totp', $user), $params, $opts);
    }

    public function disableCustom(string $user, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/users/%s/2fa/custom', $user), $params, $opts);
    }

    public function enableTOTPSeed(string $user, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/users/%s/2fa/totp/enable', $user), $params, $opts);
    }

    public function enableCustom(string $user, $params = null, $opts = null)
    {
        return $this->request('put', $this->buildPath('/users/%s/2fa/custom', $user), $params, $opts);
    }

    public function generateTOTPSeed(string $user, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/users/%s/2fa/totp/setup', $user), $params, $opts);
    }

    public function validateTOTPToken(string $user, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/users/%s/2fa/totp/check', $user), $params, $opts);
    }

    public function generateU2F(string $user, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/users/%s/2fa/u2f/setup', $user), $params, $opts);
    }

    public function enableU2F(string $user, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/users/%s/2fa/u2f/enable', $user), $params, $opts);
    }

    public function disableU2F(string $user, $params = null, $opts = null)
    {
        return $this->request('delete', $this->buildPath('/users/%s/2fa/u2f', $user), $params, $opts);
    }

    public function startU2F(string $user, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/users/%s/2fa/u2f/start', $user), $params, $opts);
    }

    public function validateU2F(string $user, $params = null, $opts = null)
    {
        return $this->request('post', $this->buildPath('/users/%s/2fa/u2f/check', $user), $params, $opts);
    }
}