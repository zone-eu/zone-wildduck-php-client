<?php

namespace Zone\Wildduck\HttpClient;

use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\UnexpectedValueException;

interface ClientInterface
{
    /**
     * @param string $method The HTTP method being used
     * @param string $absUrl The URL being requested, including domain and protocol
     * @param array $headers Headers to be used in the request (full strings, not KV pairs)
     * @param mixed $params Must be KV pairs when not uploading files otherwise anything is allowed, string is expected for file upload. Can be nested for arrays and hashes
     * @param bool $hasFile Whether $params references a file (via an @ prefix or CURLFile)
     *
     * @throws ApiConnectionException
     * @throws UnexpectedValueException
     *
     * @return array an array whose first element is raw request body, second
     *    element is HTTP status code and third array of HTTP headers
     */
    public function request(string $method, string $absUrl, array $headers, mixed $params, bool $hasFile): array;
}
