<?php

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\Archive\ListAllArchivedMessagesRequestDto;
use Zone\Wildduck\Dto\Archive\RestoreArchivedMessageRequestDto;
use Zone\Wildduck\Dto\Archive\ArchivedMessageResponseDto;
use Zone\Wildduck\Dto\Archive\RestoreArchivedMessagesRequestDto;
use Zone\Wildduck\Dto\Archive\RestoreResponseDto;
use Zone\Wildduck\Dto\Archive\RestoreTaskResponseDto;
use Zone\Wildduck\Dto\PaginatedResultDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

class ArchiveService extends AbstractService
{
    /**
     * @param string $user
     * @param ListAllArchivedMessagesRequestDto|null $params
     * @param array<string, mixed>|null $opts
     * @return PaginatedResultDto<ArchivedMessageResponseDto>
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     * @throws InvalidDatabaseException
     */
    public function all(string $user, ?ListAllArchivedMessagesRequestDto $params = null, array|null $opts = null): PaginatedResultDto
    {
        return $this->requestPaginatedDto('get', $this->buildPath('/users/%s/archived/messages', $user), $params, ArchivedMessageResponseDto::class, $opts);
    }

    /**
     * @param string $user
     * @param string $message
     * @param RestoreArchivedMessageRequestDto|null $params
     * @param array<string, mixed>|null $opts
     * @return RestoreResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function restore(string $user, string $message, RestoreArchivedMessageRequestDto|null $params = null, array|null $opts = null): RestoreResponseDto
    {
        return $this->requestDto('post', $this->buildPath('/users/%s/archived/messages/%s/restore', $user, $message), $params, RestoreResponseDto::class, $opts);
    }

    /**
     * @param string $user
     * @param RestoreArchivedMessagesRequestDto $params
     * @param array<string, mixed>|null $opts
     * @return RestoreTaskResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function restoreAll(string $user, RestoreArchivedMessagesRequestDto $params, array|null $opts = null): RestoreTaskResponseDto
    {
        return $this->requestDto('post', $this->buildPath('/users/%s/archived/restore', $user), $params, RestoreTaskResponseDto::class, $opts);
    }
}
