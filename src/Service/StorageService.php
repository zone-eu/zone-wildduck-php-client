<?php

declare(strict_types=1);

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\PaginatedResultDto;
use Zone\Wildduck\Dto\Storage\ListFilesRequestDto;
use Zone\Wildduck\Dto\Storage\StoredFileResponseDto;
use Zone\Wildduck\Dto\Storage\UploadFileRequestDto;
use Zone\Wildduck\Dto\Storage\UploadFileResponseDto;
use Zone\Wildduck\Dto\Shared\SuccessResponseDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

/**
 * Storage service for managing file storage
 */
class StorageService extends AbstractService
{
    /**
     * List stored files
     *
     * @param string $user
     * @param ListFilesRequestDto|null $params
     * @param array|null $opts
     * @return PaginatedResultDto<StoredFileResponseDto>
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     * @throws InvalidDatabaseException
     */
    public function all(string $user, ?ListFilesRequestDto $params = null, array|null $opts = null): PaginatedResultDto
    {
        return $this->requestPaginatedDto('get', $this->buildPath('/users/%s/storage', $user), $params, StoredFileResponseDto::class, $opts);
    }

    /**
     * Upload a file
     *
     * @param string $user
     * @param UploadFileRequestDto $params
     * @param array|null $opts
     * @return UploadFileResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function upload(string $user, UploadFileRequestDto $params, array|null $opts = null): UploadFileResponseDto
    {
        return $this->requestDto('post', $this->buildPath('/users/%s/storage', $user), $params, UploadFileResponseDto::class, $opts);
    }

    /**
     * Download a file (returns binary content)
     *
     * @param string $user
     * @param string $file
     * @param array|null $opts
     * @return string Binary file content
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function download(string $user, string $file, array|null $opts = null): string
    {
        $opts = $opts ?? [];
        $opts['raw'] = true;
        $response = $this->requestResponse('get', $this->buildPath('/users/%s/storage/%s', $user, $file), null, $opts);

        // When raw is true, requestResponse returns an ApiResponse object
        if ($response instanceof \Zone\Wildduck\ApiResponse) {
            return $response->body ?? '';
        }

        return $response;
    }

    /**
     * Delete a file
     *
     * @param string $user
     * @param string $file
     * @param array|null $opts
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function delete(string $user, string $file, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('delete', $this->buildPath('/users/%s/storage/%s', $user, $file), null, SuccessResponseDto::class, $opts);
    }
}
