<?php

namespace Zone\Wildduck\Service;

use Override;
use Zone\Wildduck\Collection2;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\Resource\File;
use Zone\Wildduck\Resource\Message;

class StorageService extends AbstractService
{
    /**
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function delete(string $user, string $file, array|null $params = null, array|null $opts = null): mixed
    {
        return $this->request('delete', $this->buildPath('/users/%s/storage/%s', $user, $file), $params, $opts);
    }

    /**
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function download(string $user, string $file, array|null $params = null, array|null $opts = null): mixed
    {
        $opts['raw'] = true;
        return $this->request('get', $this->buildPath('/users/%s/storage/%s', $user, $file), $params, $opts);
    }

    /**
     * @param string $user
     * @param array|null $params
     * @param array|null $opts
     *
     * @return Collection2
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     * @throws InvalidDatabaseException
     */
    public function list(string $user, array|null $params = null, array|null $opts = null): Collection2
    {
        return $this->requestCollection('get', $this->buildPath('/users/%s/storage', $user), $params, $opts);
    }


    /**
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    public function upload(
        string $user,
        string $content,
        string $filename,
        string $contentType,
        string $encoding = 'base64',
        string $cid = '',
        ?array $opts = null
    ) {
        return $this->uploadFile(
            'post',
            $this->buildPath(
                '/users/%s/storage?filename=%s&contentType=%s&encoding=%s&cid=%s',
                $user,
                $filename,
                $contentType,
                $encoding,
                $cid
            ),
            $content,
            $opts
        );
    }

    #[Override]
    public function getObjectName(): string
    {
        return File::OBJECT_NAME;
    }
}
