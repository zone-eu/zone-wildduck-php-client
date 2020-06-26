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
}