<?php

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\Dkim\CreateOrUpdateDkimRequestDto;
use Zone\Wildduck\Dto\Dkim\DkimPaginatedResponseDto;
use Zone\Wildduck\Dto\Dkim\DkimResolveResponseDto;
use Zone\Wildduck\Dto\Dkim\DkimResponseDto;
use Zone\Wildduck\Dto\Dkim\ListAllDkimRequestDto;
use Zone\Wildduck\Dto\User\UserInfoResponseDto;
use Zone\Wildduck\Dto\PaginatedResultDto;
use Zone\Wildduck\Dto\Shared\SuccessResponseDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

class DkimService extends AbstractService
{
    /**
     * @param CreateOrUpdateDkimRequestDto $params
     * @param array<string, mixed>|null $opts
     * @return DkimResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function create(CreateOrUpdateDkimRequestDto $params, array|null $opts = null): DkimResponseDto
    {
        return $this->requestDto('post', '/dkim', $params, DkimResponseDto::class, $opts);
    }

    /**
     * @param string $dkim
     * @param array<string, mixed>|null $opts
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function delete(string $dkim, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('delete', $this->buildPath('/dkim/%s', $dkim), null, SuccessResponseDto::class, $opts);
    }

    /**
     * @param ListAllDkimRequestDto|null $params
     * @param array<string, mixed>|null $opts
     * @return DkimPaginatedResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     * @throws InvalidDatabaseException
     */
    public function all(?ListAllDkimRequestDto $params = null, array|null $opts = null): DkimPaginatedResponseDto
    {
        $response = $this->request('get', '/dkim', $params, $opts);
        return DkimPaginatedResponseDto::fromArray($response);
    }

    /**
     * @param string $dkim
     * @param array<string, mixed>|null $opts
     * @return DkimResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function get(string $dkim, array|null $opts = null): DkimResponseDto
    {
        return $this->requestDto('get', $this->buildPath('/dkim/%s', $dkim), null, DkimResponseDto::class, $opts);
    }

    /**
     * @param string $domain
     * @param array<string, mixed>|null $opts
     * @return DkimResolveResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function resolve(string $domain, array|null $opts = null): DkimResolveResponseDto
    {
        return $this->requestDto('get', $this->buildPath('/dkim/resolve/%s', $domain), null, DkimResolveResponseDto::class, $opts);
    }
}
