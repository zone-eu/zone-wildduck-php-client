<?php

namespace Zone\Wildduck;

use ErrorException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\UnexpectedValueException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\Util\RequestOptions;

/**
 * Interface for a Wildduck client.
 */
interface WildduckClientInterface
{
    /**
     * Gets the access token used by the client to send requests.
     *
     * @return null|string the access token used by the client to send requests
     */
    public function getAccessToken(): string|null;

    /**
     * Gets the base URL for Wildduck's API.
     *
     * @return string the base URL for Wildduck's API
     */
    public function getApiBase(): string;

    /**
     * Sends a request to Wildduck's API.
     *
     * @param string $method the HTTP method
     * @param string $path the path of the request
     * @param mixed $params Must be KV pairs when not uploading files otherwise anything is allowed, string is expected for file upload. Can be nested for arrays and hashes
     * @param array|RequestOptions|null $opts the special modifiers of the request
     * @param bool $fileUpload
     *
     * @throws ApiConnectionException
     * @throws UnexpectedValueException
     * @throws AuthenticationFailedException
     * @throws RequestFailedException
     * @throws ValidationException
     * @throws InvalidAccessTokenException
     */
    public function request(string $method, string $path, mixed $params, array|RequestOptions|null $opts, bool $fileUpload = false): mixed;

	/**
	 * @param string $method
	 * @param string $path
	 * @param array|null $params
	 * @param array|RequestOptions|null $opts
	 * @return mixed
	 *
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws InvalidDatabaseException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
	public function requestCollection(string $method, string $path, array|null $params, array|RequestOptions|null $opts): mixed;

	/**
	 * @param string $method
	 * @param string $path
	 * @param array|null $params
	 * @param array|null $opts
	 * @return StreamedResponse
	 *
	 * @throws ErrorException
	 */
	public function stream(string $method, string $path, array|null $params, array|null $opts): StreamedResponse;
}
