<?php

namespace Wildduck\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use function GuzzleHttp\Psr7\parse_header;
use Psr\Http\Message\ResponseInterface;
use Wildduck\Client as WildduckClient;
use Wildduck\Exceptions\AuthenticationFailedException;
use Wildduck\Exceptions\InvalidRequestException;
use Wildduck\Exceptions\RequestFailedException;

class Request
{
    const HTTP_OK = 0;
    const HTTP_ERROR = 1;

    const CODE_INPUT_VALIDATION_ERROR = 'InputValidationError';
    const CODE_INVALID_TOKEN = 'InvalidToken';

    /**
     * @param string $uri
     * @param array $params
     * @return array|string
     * @throws RequestFailedException
     * @throws InvalidRequestException
     * @throws \ErrorException
     */
    public static function get(string $uri, array $params = [])
    {
        return self::request('get', $uri, $params);
    }

    /**
     * @param string $uri
     * @param array $params
     * @return array|string
     * @throws RequestFailedException
     * @throws InvalidRequestException
     * @throws \ErrorException
     */
    public static function post(string $uri, array $params = [])
    {
        return self::request('post', $uri, $params);
    }

    /**
     * @param string $uri
     * @param array $params
     * @return array|string
     * @throws RequestFailedException
     * @throws InvalidRequestException
     * @throws \ErrorException
     */
    public static function put(string $uri, array $params = [])
    {
        return self::request('put', $uri, $params);
    }

    /**
     * @param string $uri
     * @param array $params
     * @return array|string
     * @throws RequestFailedException
     * @throws InvalidRequestException
     * @throws \ErrorException
     */
    public static function delete(string $uri, array $params = [])
    {
        return self::request('delete', $uri, $params);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $params
     * @return array|string|ResponseInterface
     * @throws RequestFailedException
     * @throws InvalidRequestException
     * @throws \ErrorException
     * @throws AuthenticationFailedException
     */
    public static function request(string $method, string $uri, array $params = [])
    {
        $client = new Client([
            'base_uri' => WildduckClient::instance()->getHost(),
            'timeout' => config('wildduck.request_timeout'),
        ]);

        $opts = [
            'query' => [],
        ];

        if (count($params)) {
            if ($method === 'get') {
                $opts['query'] = $params;
            } else {
                // Add session and IP to POST/PUT/PATCH/DELETE requests
                if (null !== $session = WildduckClient::instance()->getSession()) {
                    $params['sess'] = $session;
                }

                if (null !== $ip = WildduckClient::instance()->getIp()) {
                    $params['ip'] = $ip;
                }

                $opts['json'] = $params;
            }
        }

        $accessToken = WildduckClient::instance()->getUserToken();
        if (null === $accessToken) {
            $accessToken = WildduckClient::instance()->getAccessToken();
        }

        $opts['headers'] = [
            'X-Access-Token' => $accessToken,
        ];

        try {
            $res = $client->request($method, $uri, $opts);
            if (WildduckClient::instance()->isRaw()) {
                return $res;
            }

            $contentType = parse_header($res->getHeader('Content-Type'))[0][0];

            if ($contentType === 'application/json') {
                $data = json_decode($res->getBody()->getContents(), true);

                if (isset($data['error'])) {
                    if (isset($data['code'])) {
                        switch ($data['code']) {
                            case self::CODE_INPUT_VALIDATION_ERROR:
                                throw new InvalidRequestException($data['error']);
                            case self::CODE_INVALID_TOKEN:
                                throw new AuthenticationFailedException($data['error']);
                            default:
                                throw new RequestFailedException($data['error'], $data['code']);
                        }
                    }

                    throw new RequestFailedException($data['error']);
                };

                if (isset($data['success'])) {
                    unset($data['success']); // No point in returning in response
                }

                return $data;
            }

            // Non-JSON response
            return $res->getBody()->getContents();
        } catch (BadResponseException $e) {
            if ($e->getResponse() !== null) {
                $body = json_decode($e->getResponse()->getBody()->getContents(), true);
                if (isset($body['code'])) {
                    switch ($body['code']) {
                        case self::CODE_INVALID_TOKEN:
                            throw new AuthenticationFailedException($body['error']);
                    }
                }

                throw new RequestFailedException($body['error'], $body['code']);
            }

            throw $e;
        } catch (GuzzleException $e) {
            $message = $e->getMessage();
            throw new \ErrorException($message, self::HTTP_ERROR);
        }
    }
}
