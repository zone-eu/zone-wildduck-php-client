<?php

declare(strict_types=1);

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\Authentication\AuthenticateRequestDto;
use Zone\Wildduck\Dto\Authentication\AuthenticationResponseDto;
use Zone\Wildduck\Dto\Authentication\PreauthRequestDto;
use Zone\Wildduck\Dto\Authentication\PreauthResponseDto;
use Zone\Wildduck\Dto\Shared\SuccessResponseDto;
use Zone\Wildduck\Dto\User\UserInfoResponseDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

/**
 * Authentication service for user authentication
 */
class AuthenticationService extends AbstractService
{
    /**
     * Authenticate a user
     *
     * @param AuthenticateRequestDto $params
     * @param array|null $opts
     * @return AuthenticationResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function authenticate(AuthenticateRequestDto $params, array|null $opts = null): AuthenticationResponseDto
    {
        return $this->requestDto('post', '/authenticate', $params, AuthenticationResponseDto::class, $opts);
    }

    /**
     * Invalidate authentication token
     *
     * @param array|null $opts
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function invalidateToken(array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('delete', '/authenticate', null, SuccessResponseDto::class, $opts);
    }

    /**
     * Pre-authenticate for token generation
     *
     * @param PreauthRequestDto $params
     * @param array|null $opts
     * @return PreauthResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function preauth(PreauthRequestDto $params, array|null $opts = null): PreauthResponseDto
    {
        return $this->requestDto('post', '/preauth', $params, PreauthResponseDto::class, $opts);
    }
}
