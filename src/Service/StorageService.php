<?php

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\WildduckObject;
use Zone\Wildduck\Util\RequestOptions;
use Override;
use Zone\Wildduck\Collection2;
use Zone\Wildduck\Resource\File;

class StorageService extends AbstractService
{
	/**
	 * @param string $user
	 * @param string $file
	 * @param array|null $params
	 * @param array|null $opts
	 * @return File
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function delete(string $user, string $file, array|null $params = null, array|null $opts = null): File
    {
        return $this->request('delete', $this->buildPath('/users/%s/storage/%s', $user, $file), $params, $opts);
    }

	/**
	 * @param string $user
	 * @param string $file
	 * @param array|null $params
	 * @param array|null $opts
	 *
	 * @return File
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function download(string $user, string $file, array|null $params = null, array|null $opts = null): File
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
	 * @param string $user
	 * @param string $filename
	 * @param string $contentType
	 * @param array $content
	 * @param array|RequestOptions|null $opts
	 *
	 * @return WildduckObject|string
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function upload(string $user, string $filename, string $contentType, array $content, array|RequestOptions|null $opts = null): WildduckObject|string
    {
        return $this->file('post', $this->buildPath('/users/%s/storage?filename=%s&contentType=%s', $user, $filename, $contentType), $content, $opts);
    }

    #[Override]
    public function getObjectName(): string
    {
        return File::OBJECT_NAME;
    }
}
