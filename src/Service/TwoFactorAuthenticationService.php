<?php

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\Shared\SuccessResponseDto;
use Zone\Wildduck\Dto\TwoFactorAuth\EnableCustomTwoFactorRequestDto;
use Zone\Wildduck\Dto\TwoFactorAuth\EnableTotpRequestDto;
use Zone\Wildduck\Dto\TwoFactorAuth\GenerateTotpRequestDto;
use Zone\Wildduck\Dto\TwoFactorAuth\ValidateTotpRequestDto;
use Zone\Wildduck\Dto\TwoFactorAuth\WebAuthnAuthenticationAssertionRequestDto;
use Zone\Wildduck\Dto\TwoFactorAuth\WebAuthnAuthenticationChallengeRequestDto;
use Zone\Wildduck\Dto\TwoFactorAuth\WebAuthnRegistrationAttestationRequestDto;
use Zone\Wildduck\Dto\TwoFactorAuth\WebAuthnRegistrationChallengeRequestDto;
use Zone\Wildduck\Dto\TwoFactorAuth\DeleteWebAuthnCredentialResponseDto;
use Zone\Wildduck\Dto\TwoFactorAuth\TotpSeedResponseDto;
use Zone\Wildduck\Dto\TwoFactorAuth\WebAuthnCredentialsResponseDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

class TwoFactorAuthenticationService extends AbstractService
{
    /**
     * @param string $user
     * @param array<string, mixed>|null $opts
     * @return SuccessResponseDto
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function disable(string $user, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('delete', $this->buildPath('/users/%s/2fa', $user), null, SuccessResponseDto::class, $opts);
    }

    /**
     * @param string $user
     * @param array<string, mixed>|null $opts
     * @return SuccessResponseDto
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function disableTOTPAuth(string $user, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('delete', $this->buildPath('/users/%s/2fa/totp', $user), null, SuccessResponseDto::class, $opts);
    }

    /**
     * Disable custom 2FA for a user
     *
     * @param string $user
     * @param array<string, mixed>|null $opts
     * @return SuccessResponseDto
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function disableCustom(string $user, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('delete', $this->buildPath('/users/%s/2fa/custom', $user), null, SuccessResponseDto::class, $opts);
    }

    /**
     * @param string $user
     * @param EnableTotpRequestDto $params
     * @param array<string, mixed>|null $opts
     * @return SuccessResponseDto
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function enableTOTPSeed(string $user, EnableTotpRequestDto $params, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('post', $this->buildPath('/users/%s/2fa/totp/enable', $user), $params, SuccessResponseDto::class, $opts);
    }

    /**
     * @param string $user
     * @param EnableCustomTwoFactorRequestDto $params
     * @param array<string, mixed>|null $opts
     * @return SuccessResponseDto
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function enableCustom(string $user, EnableCustomTwoFactorRequestDto $params, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('put', $this->buildPath('/users/%s/2fa/custom', $user), $params, SuccessResponseDto::class, $opts);
    }

    /**
     * @param string $user
     * @param GenerateTotpRequestDto $params
     * @param array<string, mixed>|null $opts
     * @return TotpSeedResponseDto
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function generateTOTPSeed(string $user, GenerateTotpRequestDto $params, array|null $opts = null): TotpSeedResponseDto
    {
        return $this->requestDto('post', $this->buildPath('/users/%s/2fa/totp/setup', $user), $params, TotpSeedResponseDto::class, $opts);
    }

    /**
     * @param string $user
     * @param ValidateTotpRequestDto $params
     * @param array<string, mixed>|null $opts
     * @return SuccessResponseDto
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function validateTOTPToken(string $user, ValidateTotpRequestDto $params, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('post', $this->buildPath('/users/%s/2fa/totp/check', $user), $params, SuccessResponseDto::class, $opts);
    }

    /**
     * Gets the challenge used to register a new WebAuthN key
     *
     * @param string $user
     * @param array<string, mixed>|null $opts
     * @return WebAuthnCredentialsResponseDto
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function webAuthNCredentials(string $user, array|null $opts = null): WebAuthnCredentialsResponseDto
    {
        return $this->requestDto('get', $this->buildPath('/users/%s/2fa/webauthn/credentials', $user), null, WebAuthnCredentialsResponseDto::class, $opts);
    }

    /**
     * Gets the challenge used to register a new WebAuthN key
     *
     * @param string $user
     * @param WebAuthnRegistrationChallengeRequestDto $params
     * @param array<string, mixed>|null $opts
     * @return array<string, mixed>
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function webAuthNRegistrationChallenge(string $user, WebAuthnRegistrationChallengeRequestDto $params, array|null $opts = null): array
    {
        return $this->request('post', $this->buildPath('/users/%s/2fa/webauthn/registration-challenge', $user), $params, $opts);
    }

    /**
     * Attests the credential used to register a new WebAuthN key
     *
     * @param string $user
     * @param WebAuthnRegistrationAttestationRequestDto $params
     * @param array<string, mixed>|null $opts
     * @return array<string, mixed>
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function webAuthNRegistrationAttestation(string $user, WebAuthnRegistrationAttestationRequestDto $params, array|null $opts = null): array
    {
        return $this->request('post', $this->buildPath('/users/%s/2fa/webauthn/registration-attestation', $user), $params, $opts);
    }

    /**
     * Removes the credential for the user
     *
     * @param string $user
     * @param string $credentialId
     * @param array<string, mixed>|null $opts
     * @return DeleteWebAuthnCredentialResponseDto
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function webAuthNRemoveCredential(string $user, string $credentialId, array|null $opts = null): DeleteWebAuthnCredentialResponseDto
    {
        return $this->requestDto('delete', $this->buildPath('/users/%s/2fa/webauthn/credentials/%s', $user, $credentialId), null, DeleteWebAuthnCredentialResponseDto::class, $opts);
    }

    /**
     * Gets the challenge used for authentication with a WebAuthN compatible key
     *
     * @param string $user
     * @param WebAuthnAuthenticationChallengeRequestDto $params
     * @param array<string, mixed>|null $opts
     * @return array<string, mixed>
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function webAuthNAuthenticationChallenge(string $user, WebAuthnAuthenticationChallengeRequestDto $params, array|null $opts = null): array
    {
        return $this->request('post', $this->buildPath('/users/%s/2fa/webauthn/authentication-challenge', $user), $params, $opts);
    }

    /**
     * Asserts that the credential returned from the WebAuthN compatible key is allowed for the user
     *
     * @param string $user
     * @param WebAuthnAuthenticationAssertionRequestDto $params
     * @param array<string, mixed>|null $opts
     * @return array<string, mixed>
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function webAuthNAuthenticationAssertion(string $user, WebAuthnAuthenticationAssertionRequestDto $params, array|null $opts = null): array
    {
        return $this->request('post', $this->buildPath('/users/%s/2fa/webauthn/authentication-assertion', $user), $params, $opts);
    }
}
