<?php

namespace Zone\Wildduck;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
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
     * @param array|string|null $params Must be KV pairs when not uploading files otherwise anything is allowed, string is expected for file upload. Can be nested for arrays and hashes
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
    public function request(string $method, string $path, array|string|null $params, array|RequestOptions|null $opts, bool $fileUpload = false): ApiResponse|string;

    /**
     * Sends a streaming request to Wildduck's API (for EventSource).
     *
     * @param string $method the HTTP method
     * @param string $path the path of the request
     * @param array|null $params the parameters of the request
     * @param array|object|null $opts the special modifiers of the request
     *
     * @return StreamedResponse the streamed response
     */
    public function stream(string $method, string $path, array|null $params, array|object|null $opts): StreamedResponse;
}
