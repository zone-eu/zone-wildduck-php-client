<?php

namespace Zone\Wildduck;

/**
 * Interface for a Wildduck client.
 */
interface WildduckClientInterface
{
    /**
     * Gets the API key used by the client to send requests.
     *
     * @return null|string the API key used by the client to send requests
     */
//    public function getApiKey();

    /**
     * Gets the access token used by the client to send requests.
     *
     * @return null|string the access token used by the client to send requests
     */
    public function getAccessToken();

    /**
     * Gets the base URL for Wildduck's API.
     *
     * @return string the base URL for Wildduck's API
     */
    public function getApiBase();

    /**
     * Sends a request to Wildduck's API.
     *
     * @param string $method the HTTP method
     * @param string $path the path of the request
     * @param array $params the parameters of the request
     * @param array|\Zone\Wildduck\Util\RequestOptions $opts the special modifiers of the request
     *
     * @return \Zone\Wildduck\WildduckObject the object returned by Wildduck's API
     */
    public function request($method, $path, $params, $opts);
}
