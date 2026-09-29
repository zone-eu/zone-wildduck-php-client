<?php

declare(strict_types=1);

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\ScopedToken\CreateScopedTokenResponseDto;
use Zone\Wildduck\Dto\Shared\SuccessResponseDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\MasterTokenNotEligibleException;
use Zone\Wildduck\Exception\MasterTokenRequiredException;
use Zone\Wildduck\Exception\MfaRequiredException;
use Zone\Wildduck\Exception\PasswordChangeRequiredException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ScopedTokenNotFoundException;
use Zone\Wildduck\Exception\UnsupportedAuthScopeException;
use Zone\Wildduck\Exception\ValidationException;

/**
 * Scoped authentication token service.
 *
 * Exchanges the user's master API token for an independent token restricted
 * to a single scope and revokes scoped login sessions by their session
 * identifier. The service must be used with a client authenticated by the
 * user's master API token.
 */
class ScopedTokenService extends AbstractService
{
    /**
     * Exchange the master API token for a scoped authentication token
     *
     * The response contains the session identifier and the plaintext bearer
     * token, which is returned only once.
     *
     * @param string $scope
     * @param array<string, mixed>|null $opts
     * @return CreateScopedTokenResponseDto
     *
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws MasterTokenNotEligibleException
     * @throws MasterTokenRequiredException
     * @throws MfaRequiredException
     * @throws PasswordChangeRequiredException
     * @throws RequestFailedException
     * @throws UnsupportedAuthScopeException
     * @throws ValidationException
     */
    public function createScopedToken(string $scope, array|null $opts = null): CreateScopedTokenResponseDto
    {
        return $this->requestDto(
            'post',
            $this->buildPath('/authenticate/%s', $scope),
            null,
            CreateScopedTokenResponseDto::class,
            $opts
        );
    }

    /**
     * Revoke a scoped login session by its session identifier
     *
     * @param string $scope
     * @param string $id
     * @param array<string, mixed>|null $opts
     * @return SuccessResponseDto
     *
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws MasterTokenRequiredException
     * @throws RequestFailedException
     * @throws ScopedTokenNotFoundException
     * @throws UnsupportedAuthScopeException
     * @throws ValidationException
     */
    public function revokeScopedToken(string $scope, string $id, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto(
            'delete',
            $this->buildPath('/authenticate/%s/%s', $scope, $id),
            null,
            SuccessResponseDto::class,
            $opts
        );
    }
}
