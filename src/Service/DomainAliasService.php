<?php

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\DomainAlias\CreateDomainAliasRequestDto;
use Zone\Wildduck\Dto\DomainAlias\DomainAliasResponseDto;
use Zone\Wildduck\Dto\DomainAlias\DomainAliasSuccessResponseDto;
use Zone\Wildduck\Dto\DomainAlias\ListAllDomainAliasesRequestDto;
use Zone\Wildduck\Dto\User\UserInfoResponseDto;
use Zone\Wildduck\Dto\PaginatedResultDto;
use Zone\Wildduck\Dto\Shared\SuccessResponseDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

class DomainAliasService extends AbstractService
{
    /**
     * @param CreateDomainAliasRequestDto $params
     * @param array<string, mixed>|null $opts
     * @return DomainAliasSuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function create(CreateDomainAliasRequestDto $params, array|null $opts = null): DomainAliasSuccessResponseDto
    {
        return $this->requestDto('post', '/domainaliases', $params, DomainAliasSuccessResponseDto::class, $opts);
    }

    /**
     * @param string $alias
     * @param array<string, mixed>|null $opts
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function delete(string $alias, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('delete', $this->buildPath('/domainaliases/%s', $alias), null, SuccessResponseDto::class, $opts);
    }

    /**
     * @param ListAllDomainAliasesRequestDto $params
     * @param array<string, mixed>|null $opts
     * @return PaginatedResultDto<DomainAliasResponseDto>
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws InvalidDatabaseException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function all(ListAllDomainAliasesRequestDto $params, array|null $opts = null): PaginatedResultDto
    {
        return $this->requestPaginatedDto('get', '/domainaliases', $params, DomainAliasResponseDto::class, $opts);
    }

    /**
     * @param string $alias
     * @param array<string, mixed>|null $opts
     * @return DomainAliasResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function get(string $alias, array|null $opts = null): DomainAliasResponseDto
    {
        return $this->requestDto('get', $this->buildPath('/domainaliases/%s', $alias), null, DomainAliasResponseDto::class, $opts);
    }

    /**
     * @param string $alias
     * @param array<string, mixed>|null $opts
     * @return DomainAliasSuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function resolve(string $alias, array|null $opts = null): DomainAliasSuccessResponseDto
    {
        return $this->requestDto('get', $this->buildPath('/domainaliases/resolve/%s', $alias), null, DomainAliasSuccessResponseDto::class, $opts);
    }
}
