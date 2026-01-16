<?php

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\ApplicationPassword\CreateApplicationPasswordRequestDto;
use Zone\Wildduck\Dto\ApplicationPassword\ApplicationPasswordResponseDto;
use Zone\Wildduck\Dto\ApplicationPassword\CreateApplicationPasswordResponseDto;
use Zone\Wildduck\Dto\ApplicationPassword\DeleteApplicationPasswordRequestDto;
use Zone\Wildduck\Dto\ApplicationPassword\ListAllUserApplicationPasswordRequestDto;
use Zone\Wildduck\Dto\User\UserInfoResponseDto;
use Zone\Wildduck\Dto\PaginatedResultDto;
use Zone\Wildduck\Dto\Shared\SuccessResponseDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

class ApplicationPasswordService extends AbstractService
{
    /**
     * @param string $user
     * @param CreateApplicationPasswordRequestDto $params
     * @param array<string, mixed>|null $opts
     * @return CreateApplicationPasswordResponseDto
     *
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function create(string $user, CreateApplicationPasswordRequestDto $params, array|null $opts = null): CreateApplicationPasswordResponseDto
    {
        return $this->requestDto('post', $this->buildPath('/users/%s/asps', $user), $params, CreateApplicationPasswordResponseDto::class, $opts);
    }

    /**
     * @param string $user
     * @param string $asp
     * @param array<string, mixed>|null $opts
     *
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function delete(string $user, string $asp, ?DeleteApplicationPasswordRequestDto $params = null, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('delete', $this->buildPath('/users/%s/asps/%s', $user, $asp), $params, SuccessResponseDto::class, $opts);
    }

    /**
     * @param string $user
     * @param ?ListAllUserApplicationPasswordRequestDto $params
     * @param array<string, mixed>|null $opts
     *
     * @return PaginatedResultDto<ApplicationPasswordResponseDto>
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws InvalidDatabaseException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function all(string $user, ?ListAllUserApplicationPasswordRequestDto $params = null, array|null $opts = null): PaginatedResultDto
    {
        return $this->requestPaginatedDto('get', $this->buildPath('/users/%s/asps', $user), $params, ApplicationPasswordResponseDto::class, $opts);
    }

    /**
     * @param string $user
     * @param string $asp
     * @param array<string, mixed>|null $opts
     *
     * @return ApplicationPasswordResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function get(string $user, string $asp, array|null $opts = null): ApplicationPasswordResponseDto
    {
        return $this->requestDto('get', $this->buildPath('/users/%s/asps/%s', $user, $asp), null, ApplicationPasswordResponseDto::class, $opts);
    }
}
