<?php

declare(strict_types=1);

namespace Zone\Wildduck\Service;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Zone\Wildduck\Dto\PaginatedResultDto;
use Zone\Wildduck\Dto\Shared\SuccessResponseDto;
use Zone\Wildduck\Dto\User\CancelUserDeletionRequestDto;
use Zone\Wildduck\Dto\User\CancelUserDeletionResponseDto;
use Zone\Wildduck\Dto\User\CreateUserRequestDto;
use Zone\Wildduck\Dto\User\CreatedUserResponseDto;
use Zone\Wildduck\Dto\User\DeleteUserResponseDto;
use Zone\Wildduck\Dto\User\ListUsersRequestDto;
use Zone\Wildduck\Dto\User\LogoutUserRequestDto;
use Zone\Wildduck\Dto\User\QuotaRecalculationResponseDto;
use Zone\Wildduck\Dto\User\QuotaRecalculationTaskResponseDto;
use Zone\Wildduck\Dto\User\ResetPasswordRequestDto;
use Zone\Wildduck\Dto\User\ResetPasswordResponseDto;
use Zone\Wildduck\Dto\User\ResolveUserResponseDto;
use Zone\Wildduck\Dto\User\RestoreUserInfoDto;
use Zone\Wildduck\Dto\User\UpdateUserRequestDto;
use Zone\Wildduck\Dto\User\UserResponseDto;
use Zone\Wildduck\Dto\User\UserInfoResponseDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

/**
 * User service for managing WildDuck users
 */
class UserService extends AbstractService
{
    /**
     * Get list of all users
     *
     * @param ListUsersRequestDto|null $params Query parameters for filtering users
     * @param array|null $opts
     * @return PaginatedResultDto<UserResponseDto>
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws InvalidDatabaseException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function all(?ListUsersRequestDto $params = null, array|null $opts = null): PaginatedResultDto
    {
        return $this->requestPaginatedDto('get', '/users', $params, UserResponseDto::class, $opts);
    }

    /**
     * Create a new user
     *
     * @param CreateUserRequestDto $params
     * @param array|null $opts
     * @return CreatedUserResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function create(CreateUserRequestDto $params, array|null $opts = null): CreatedUserResponseDto
    {
        return $this->requestDto('post', '/users', $params, CreatedUserResponseDto::class, $opts);
    }

    /**
     * Get user information
     *
     * @param string $id
     * @param array|null $opts
     * @return UserResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function get(string $id, array|null $opts = null): UserResponseDto
    {
        return $this->requestDto('get', $this->buildPath('/users/%s', $id), null, UserResponseDto::class, $opts);
    }

    /**
     * Update user information
     *
     * @param string $id
     * @param UpdateUserRequestDto $params
     * @param array|null $opts
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function update(string $id, UpdateUserRequestDto $params, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('put', $this->buildPath('/users/%s', $id), $params, SuccessResponseDto::class, $opts);
    }

    /**
     * Delete a user
     *
     * @param string $id
     * @param array|null $opts
     * @return DeleteUserResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function delete(string $id, array|null $opts = null): DeleteUserResponseDto
    {
        return $this->requestDto('delete', $this->buildPath('/users/%s', $id), null, DeleteUserResponseDto::class, $opts);
    }

    /**
     * Logout user (invalidate all sessions)
     *
     * @param string $id
     * @param LogoutUserRequestDto $params
     * @param array|null $opts
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function logout(string $id, LogoutUserRequestDto $params, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('put', $this->buildPath('/users/%s/logout', $id), $params, SuccessResponseDto::class, $opts);
    }

    /**
     * Stream user updates
     *
     * @param string $id
     * @param array|null $params
     * @param array|null $opts
     * @return StreamedResponse
     */
    public function updateStream(string $id, array|null $params = null, array|null $opts = null): StreamedResponse
    {
        return $this->stream('get', $this->buildPath('/users/%s/updates', $id), $params, $opts);
    }

    /**
     * Recalculate quota for a user
     *
     * @param string $id
     * @param array|null $opts
     * @return QuotaRecalculationResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function recalculateQuota(string $id, array|null $opts = null): QuotaRecalculationResponseDto
    {
        return $this->requestDto('post', $this->buildPath('/users/%s/quota/reset', $id), null, QuotaRecalculationResponseDto::class, $opts);
    }

    /**
     * Recalculate quota for all users
     *
     * @param array|null $opts
     * @return QuotaRecalculationTaskResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function recalculateAllUserQuotas(array|null $opts = null): QuotaRecalculationTaskResponseDto
    {
        return $this->requestDto('post', '/quota/reset', null, QuotaRecalculationTaskResponseDto::class, $opts);
    }

    /**
     * Reset user password
     *
     * @param string $id
     * @param ResetPasswordRequestDto $params
     * @param array|null $opts
     * @return ResetPasswordResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function resetPassword(string $id, ResetPasswordRequestDto $params, array|null $opts = null): ResetPasswordResponseDto
    {
        return $this->requestDto('post', $this->buildPath('/users/%s/password/reset', $id), $params, ResetPasswordResponseDto::class, $opts);
    }

    /**
     * Resolve username to user ID
     *
     * @param string $username
     * @param array|null $opts
     * @return ResolveUserResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function getIdByUsername(string $username, array|null $opts = null): ResolveUserResponseDto
    {
        return $this->requestDto('get', $this->buildPath('/users/resolve/%s', $username), null, ResolveUserResponseDto::class, $opts);
    }

    /**
     * Cancel user deletion task
     *
     * @param string $id
     * @param CancelUserDeletionRequestDto $params
     * @param array|null $opts
     * @return CancelUserDeletionResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function cancelDeletion(string $id, CancelUserDeletionRequestDto $params, array|null $opts = null): CancelUserDeletionResponseDto
    {
        return $this->requestDto('delete', $this->buildPath('/users/%s/restore', $id), $params, CancelUserDeletionResponseDto::class, $opts);
    }

    /**
     * Get restore info for a deleted user
     *
     * @param string $id
     * @param array|null $opts
     * @return RestoreUserInfoDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function getRestoreInfo(string $id, array|null $opts = null): RestoreUserInfoDto
    {
        return $this->requestDto('get', $this->buildPath('/users/%s/restore', $id), null, RestoreUserInfoDto::class, $opts);
    }

    /**
     * Resolve username to user ID
     *
     * @param string $username
     * @param array|null $opts
     * @return UserResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function resolveUsername(string $username, array|null $opts = null): UserResponseDto
    {
        return $this->requestDto('get', $this->buildPath('/users/resolve/%s', $username), null, UserResponseDto::class, $opts);
    }
}
