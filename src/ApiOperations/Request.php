<?php

namespace Zone\Wildduck\ApiOperations;

use Zone\Wildduck\Exception\InvalidArgumentException;
use Zone\Wildduck\ApiRequestor;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\UnexpectedValueException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\Util\RequestOptions;

/**
 * Trait for resources that need to make API requests.
 *
 * This trait should only be applied to classes that derive from WildduckObject.
 *
 */
trait Request
{
	/**
	 * @param array|null $params The list of parameters to validate
	 *
	 */
    private static function _validateParams(array|null $params = null): void
    {
        if ($params && !is_array($params)) {
            $message = 'You must pass an array as the first argument to Wildduck API method calls.';

            throw new InvalidArgumentException($message);
        }
    }

    /**
     * @param string $method HTTP method ('get', 'post', etc.)
     * @param string $url URL for the request
     * @param array $params list of parameters for the request
     * @param array|null|string|RequestOptions $options
     *
     * @return array tuple containing (the JSON response, $options)
     * @throws ApiConnectionException
     * @throws UnexpectedValueException
     * @throws AuthenticationFailedException
     * @throws RequestFailedException
     * @throws ValidationException
     * @throws InvalidAccessTokenException|InvalidDatabaseException*@throws InvalidDatabaseException
     *
     */
    private function _request(string $method, string $url, array $params = [], array|null|string|RequestOptions $options = null): array
    {
        $opts = $this->_opts->merge($options);
        [$resp, $options] = static::_staticRequest($method, $url, $params, $opts);
        $this->setLastResponse($resp);

        return [$resp->json, $options];
    }

    /**
     * @param string $method HTTP method ('get', 'post', etc.)
     * @param string $url URL for the request
     * @param array $params list of parameters for the request
     * @param array|null|string|RequestOptions $options
     *
     * @return array tuple containing (the response, $options)
     *
     * @throws ApiConnectionException
     * @throws UnexpectedValueException
     * @throws AuthenticationFailedException
     * @throws RequestFailedException
     * @throws ValidationException
     * @throws InvalidAccessTokenException
     * @throws InvalidDatabaseException
     *
     */
    private static function _staticRequest(string $method, string $url, array $params, array|null|string|RequestOptions $options): array
    {
        $opts = RequestOptions::parse($options);
        $baseUrl = $opts->apiBase ?? static::baseUrl();
        $requestor = new ApiRequestor($opts->accessToken, $baseUrl);
        [$response, $opts->apiKey] = $requestor->request($method, $url, $params, $opts->headers);
        $opts->discardNonPersistentHeaders();

        return [$response, $opts];
    }
}
