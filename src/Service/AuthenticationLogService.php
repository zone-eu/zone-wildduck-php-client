<?php

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\Authentication\AuthLogEventResponseDto;
use Zone\Wildduck\Dto\Authentication\AuthLogListRequestDto;
use Zone\Wildduck\Dto\Authentication\AuthlogPaginatedResponseDto;
use Zone\Wildduck\Dto\PaginatedResultDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

class AuthenticationLogService extends AbstractService
{
    /**
     * @param string $user
     * @param AuthLogListRequestDto $params
     * @param array<string, mixed>|null $opts
     * @return AuthlogPaginatedResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws InvalidDatabaseException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function all(string $user, AuthLogListRequestDto $params, array|null $opts = null): AuthlogPaginatedResponseDto
    {
        $response = $this->request('get', $this->buildPath('/users/%s/authlog', $user), $params, $opts);
        return AuthlogPaginatedResponseDto::fromArray($response);
    }

    /**
     * @param string $user
     * @param string $event
     * @param array<string, mixed>|null $opts
     * @return AuthLogEventResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function get(string $user, string $event, array|null $opts = null): AuthLogEventResponseDto
    {
        return $this->requestDto('get', $this->buildPath('/users/%s/authlog/%s', $user, $event), null, AuthLogEventResponseDto::class, $opts);
    }
}
