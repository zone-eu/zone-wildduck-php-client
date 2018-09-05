<?php

namespace Wildduck\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Wildduck\Client as WildduckClient;
use Wildduck\Exceptions\InvalidRequestException;
use Wildduck\Exceptions\RequestFailedException;

class Request
{
    const HTTP_OK = 0;
    const HTTP_ERROR = 1;

    const CODE_INPUT_VALIDATION_ERROR = 'InputValidationError';

    /**
     * @param string $uri
     * @param array $params
     * @return array
     * @throws RequestFailedException
     * @throws InvalidRequestException
     * @throws \ErrorException
     */
    public static function get(string $uri, array $params = []) : array
    {
        return self::request('get', $uri, $params);
    }

    /**
     * @param string $uri
     * @param array $params
     * @return array
     * @throws RequestFailedException
     * @throws InvalidRequestException
     * @throws \ErrorException
     */
    public static function post(string $uri, array $params = []) : array
    {
        return self::request('post', $uri, $params);
    }

    /**
     * @param string $uri
     * @param array $params
     * @return array
     * @throws RequestFailedException
     * @throws InvalidRequestException
     * @throws \ErrorException
     */
    public static function put(string $uri, array $params = []) : array
    {
        return self::request('put', $uri, $params);
    }

    /**
     * @param string $uri
     * @param array $params
     * @return mixed|\Psr\Http\Message\array
     * @throws RequestFailedException
     * @throws InvalidRequestException
     * @throws \ErrorException
     */
    public static function delete(string $uri, array $params = []) : array
    {
        return self::request('delete', $uri, $params);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $params
     * @return array
     * @throws RequestFailedException
     * @throws InvalidRequestException
     * @throws \ErrorException
     */
    public static function request(string $method, string $uri, array $params = []) : array
    {
        $client = new Client([
            'base_uri' => WildduckClient::instance()->getHost(),
            'timeout' => 2.0,
        ]);
        
        $opts = [];
        
        if (count($params)) {
            if ($method === 'get') {
                $opts = ['query' => $params];
            } else {
                $opts = ['json' => $params];
            }
        }

        try {
            $res = $client->request($method, $uri, $opts);
            $data = json_decode($res->getBody()->getContents(), true);

            if (isset($data['error'])) {
                if (isset($data['code']) && $data['code'] === self::CODE_INPUT_VALIDATION_ERROR) {
                    throw new InvalidRequestException($data['error']);
                }
            };

            if (isset($data['success'])) {
                unset($data['success']); // No point in returning in response
            }

            return $data;
        } catch (BadResponseException $e) {
            throw $e;
        } catch (GuzzleException $e) {
            $message = $e->getMessage();
            throw new \ErrorException($message, self::HTTP_ERROR, $e);
        }
    }
}
