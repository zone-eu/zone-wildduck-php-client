<?php

declare(strict_types=1);

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\Autoreply\AutoreplyResponseDto;
use Zone\Wildduck\Dto\Autoreply\AutoreplyUpdateResponseDto;
use Zone\Wildduck\Dto\Autoreply\UpdateAutoreplyRequestDto;
use Zone\Wildduck\Dto\Shared\SuccessResponseDto;
use Zone\Wildduck\Dto\User\UserInfoResponseDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

/**
 * Autoreply service for managing user autoreplies
 */
class AutoreplyService extends AbstractService
{
    /**
     * Delete autoreply
     *
     * @param string $user
     * @param array|null $opts
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function delete(string $user, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('delete', $this->buildPath('/users/%s/autoreply', $user), null, SuccessResponseDto::class, $opts);
    }

    /**
     * Get autoreply settings
     *
     * @param string $user
     * @param array|null $opts
     * @return AutoreplyResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function get(string $user, array|null $opts = null): AutoreplyResponseDto
    {
        return $this->requestDto('get', $this->buildPath('/users/%s/autoreply', $user), null, AutoreplyResponseDto::class, $opts);
    }

    /**
     * Update autoreply settings
     *
     * @param string $user
     * @param UpdateAutoreplyRequestDto $params
     * @param array|null $opts
     * @return AutoreplyUpdateResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function update(string $user, UpdateAutoreplyRequestDto $params, array|null $opts = null): AutoreplyUpdateResponseDto
    {
        return $this->requestDto('put', $this->buildPath('/users/%s/autoreply', $user), $params, AutoreplyUpdateResponseDto::class, $opts);
    }
}
