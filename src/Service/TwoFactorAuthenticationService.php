<?php

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Collection2;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\WildduckObject;

class TwoFactorAuthenticationService extends AbstractService
{

    /**
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function disable(string $user, $params = null, $opts = null): WildduckObject
    {
        return $this->request('delete', $this->buildPath('/users/%s/2fa', $user), $params, $opts);
    }

    /**
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function disableTOTPAuth(string $user, $params = null, $opts = null): WildduckObject
    {
        return $this->request('delete', $this->buildPath('/users/%s/2fa/totp', $user), $params, $opts);
    }

    /**
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function disableCustom(string $user, $params = null, $opts = null): WildduckObject
    {
        return $this->request('delete', $this->buildPath('/users/%s/2fa/custom', $user), $params, $opts);
    }

    /**
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function enableTOTPSeed(string $user, $params = null, $opts = null): WildduckObject
    {
        return $this->request('post', $this->buildPath('/users/%s/2fa/totp/enable', $user), $params, $opts);
    }

    /**
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function enableCustom(string $user, $params = null, $opts = null): WildduckObject
    {
        return $this->request('put', $this->buildPath('/users/%s/2fa/custom', $user), $params, $opts);
    }

    /**
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function generateTOTPSeed(string $user, $params = null, $opts = null): WildduckObject
    {
        return $this->request('post', $this->buildPath('/users/%s/2fa/totp/setup', $user), $params, $opts);
    }

    /**
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function validateTOTPToken(string $user, $params = null, $opts = null): WildduckObject
    {
        return $this->request('post', $this->buildPath('/users/%s/2fa/totp/check', $user), $params, $opts);
    }

    /**
     * Gets the challenge used to register a new WebAuthN key
     *
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function webAuthNCredentials(string $user, $params = null, $opts = null): Collection2
    {
        return $this->requestCollection('get', $this->buildPath('/users/%s/2fa/webauthn/credentials', $user), $params, $opts);
    }

    /**
     * Gets the challenge used to register a new WebAuthN key
     *
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function webAuthNRegistrationChallenge(string $user, $params = null, $opts = null): WildduckObject
    {
        return $this->request('post', $this->buildPath('/users/%s/2fa/webauthn/registration-challenge', $user), $params, $opts);
    }

    /**
     * Attests the credential used to register a new WebAuthN key
     *
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function webAuthNRegistrationAttestation(string $user, $params = null, $opts = null): WildduckObject
    {
        return $this->request('post', $this->buildPath('/users/%s/2fa/webauthn/registration-attestation', $user), $params, $opts);
    }

    /**
     * Removes the credential for the user
     *
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function webAuthNRemoveCredential(string $user, string $credentialId, $params = null, $opts = null): WildduckObject
    {
        return $this->request('delete', $this->buildPath('/users/%s/2fa/webauthn/credentials/%s', $user, $credentialId), $params, $opts);
    }

    /**
     * Gets the challenge used for authentication with a WebAuthN compatible key
     *
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function webAuthNAuthenticationChallenge(string $user, $params = null, $opts = null): WildduckObject
    {
        return $this->request('post', $this->buildPath('/users/%s/2fa/webauthn/authentication-challenge', $user), $params, $opts);
    }

    /**
     * Asserts that the credential returned from the WebAuthN compatible key is allowed for the user
     *
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function webAuthNAuthenticationAssertion(string $user, $params = null, $opts = null): WildduckObject
    {
        return $this->request('post', $this->buildPath('/users/%s/2fa/webauthn/authentication-assertion', $user), $params, $opts);
    }
}