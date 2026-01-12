<?php

declare(strict_types=1);

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\DomainAccess\CreateAllowedDomainRequestDto;
use Zone\Wildduck\Dto\DomainAccess\CreateBlockedDomainRequestDto;
use Zone\Wildduck\Dto\DomainAccess\DomainAccessResponseDto;
use Zone\Wildduck\Dto\DomainAccess\DomainAccessSuccessResponseDto;
use Zone\Wildduck\Dto\PaginatedResultDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

/**
 * Domain Access service for managing domain allow/blocklists
 */
class DomainAccessService extends AbstractService
{
    /**
     * Create an allowed domain
     *
     * @param string $tag Tag identifier
     * @param CreateAllowedDomainRequestDto $params
     * @param array|null $opts
     * @return DomainAccessSuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function createAllowedDomain(string $tag, CreateAllowedDomainRequestDto $params, array|null $opts = null): DomainAccessSuccessResponseDto
    {
        return $this->requestDto('post', $this->buildPath('/domainaccess/%s/allow', $tag), $params, DomainAccessSuccessResponseDto::class, $opts);
    }

    /**
     * Get all allowed domains for a tag
     *
     * @param string $tag Tag identifier
     * @param array|null $opts
     * @return PaginatedResultDto<DomainAccessResponseDto>
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function getAllowedDomains(string $tag, array|null $opts = null): PaginatedResultDto
    {
        return $this->requestPaginatedDto('get', $this->buildPath('/domainaccess/%s/allow', $tag), null, DomainAccessResponseDto::class, $opts);
    }

    /**
     * Create a blocked domain
     *
     * @param string $tag Tag identifier
     * @param CreateBlockedDomainRequestDto $params
     * @param array|null $opts
     * @return DomainAccessSuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function createBlockedDomain(string $tag, CreateBlockedDomainRequestDto $params, array|null $opts = null): DomainAccessSuccessResponseDto
    {
        return $this->requestDto('post', $this->buildPath('/domainaccess/%s/block', $tag), $params, DomainAccessSuccessResponseDto::class, $opts);
    }

    /**
     * Get all blocked domains for a tag
     *
     * @param string $tag Tag identifier
     * @param array|null $opts
     * @return PaginatedResultDto<DomainAccessResponseDto>
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function getBlockedDomains(string $tag, array|null $opts = null): PaginatedResultDto
    {
        return $this->requestPaginatedDto('get', $this->buildPath('/domainaccess/%s/block', $tag), null, DomainAccessResponseDto::class, $opts);
    }

    /**
     * Delete a domain from allow/blocklist
     *
     * @param string $domain Domain name
     * @param array|null $opts
     * @return DomainAccessSuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function deleteDomainListing(string $domain, array|null $opts = null): DomainAccessSuccessResponseDto
    {
        return $this->requestDto('delete', $this->buildPath('/domainaccess/%s', $domain), null, DomainAccessSuccessResponseDto::class, $opts);
    }
}
