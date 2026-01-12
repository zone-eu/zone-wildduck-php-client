<?php

declare(strict_types=1);

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\Filter\ListAllFiltersRequestDto;
use Zone\Wildduck\Dto\Filter\ListUserFiltersRequestDto;
use Zone\Wildduck\Dto\Filter\CreateFilterRequestDto;
use Zone\Wildduck\Dto\Filter\FilterResponseDto;
use Zone\Wildduck\Dto\Filter\ListAllFiltersResponseDto;
use Zone\Wildduck\Dto\Filter\UpdateFilterRequestDto;
use Zone\Wildduck\Dto\Filter\UserFiltersResponseDto;
use Zone\Wildduck\Dto\PaginatedResultDto;
use Zone\Wildduck\Dto\Shared\SuccessResponseDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

/**
 * Filter service for managing user filters
 */
class FilterService extends AbstractService
{
    /**
     * Create a new filter
     *
     * @param string $user
     * @param CreateFilterRequestDto $params
     * @param array|null $opts
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function create(string $user, CreateFilterRequestDto $params, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('post', $this->buildPath('/users/%s/filters', $user), $params, SuccessResponseDto::class, $opts);
    }

    /**
     * Delete a filter
     *
     * @param string $user
     * @param string $filter
     * @param array|null $opts
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function delete(string $user, string $filter, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('delete', $this->buildPath('/users/%s/filters/%s', $user, $filter), null, SuccessResponseDto::class, $opts);
    }

    /**
     * List all filters
     *
     * @param ListAllFiltersRequestDto|null $params
     * @param array|null $opts
     * @return PaginatedResultDto<ListAllFiltersResponseDto>
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     * @throws InvalidDatabaseException
     */
    public function all(?ListAllFiltersRequestDto $params = null, array|null $opts = null): PaginatedResultDto
    {
        return $this->requestPaginatedDto('get', '/filters', $params, ListAllFiltersResponseDto::class, $opts);
    }

    /**
     * List all filters for a user
     *
     * @param string $user
     * @param ListUserFiltersRequestDto|null $params
     * @param array|null $opts
     * @return UserFiltersResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     * @throws InvalidDatabaseException
     */
    public function userAll(string $user, ?ListUserFiltersRequestDto $params = null, array|null $opts = null): UserFiltersResponseDto
    {
        return $this->requestDto('get', $this->buildPath('/users/%s/filters', $user), $params, UserFiltersResponseDto::class, $opts);
    }

    /**
     * Get filter information
     *
     * @param string $user
     * @param string $filter
     * @param array|null $opts
     * @return FilterResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function get(string $user, string $filter, array|null $opts = null): FilterResponseDto
    {
        return $this->requestDto('get', $this->buildPath('/users/%s/filters/%s', $user, $filter), null, FilterResponseDto::class, $opts);
    }

    /**
     * Update a filter
     *
     * @param string $user
     * @param string $filter
     * @param UpdateFilterRequestDto $params
     * @param array|null $opts
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function update(string $user, string $filter, UpdateFilterRequestDto $params, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('put', $this->buildPath('/users/%s/filters/%s', $user, $filter), $params, SuccessResponseDto::class, $opts);
    }
}
