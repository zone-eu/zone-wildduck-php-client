<?php

namespace Wildduck\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Wildduck\Client as WildduckClient;

/**
 * Class Request
 * @package Wildduck\Http
 */
class Request
{
    const HTTP_OK = 0;
    const HTTP_ERROR = 1;

    /**
     * @param string $uri
     * @param array $params
     * @return array
     */
    public static function get(string $uri, array $params = []) : array
    {
        return self::request('get', $uri, $params);
    }

    /**
     * @param string $uri
     * @param array $params
     * @return array
     */
    public static function post(string $uri, array $params = []) : array
    {
        return self::request('post', $uri, $params);
    }

    /**
     * @param string $uri
     * @param array $params
     * @return array
     */
    public static function put(string $uri, array $params = []) : array
    {
        return self::request('put', $uri, $params);
    }

    /**
     * @param string $uri
     * @param array $params
     * @return mixed|\Psr\Http\Message\array
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

            $body = json_decode($res->getBody()->getContents(), true);

            if (isset($body['error'])) {
                return [
                    'code' => self::HTTP_ERROR,
                    'status_code' => $res->getStatusCode(),
                    'message' => $body['error'],
                ];
            }

            return [
                'code' => self::HTTP_OK,
                'data' => $body,
            ];
        } catch (ServerException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $message = json_decode($e->getResponse()->getBody()->getContents(), true);

            return [
                'code' => self::HTTP_ERROR,
                'status_code' => $statusCode,
                'message' => $message,
            ];
        } catch (GuzzleException $e) {
            $message = $e->getMessage();

            $response = [
                'code' => self::HTTP_ERROR,
                'message' => $message,
            ];

            if (WildduckClient::instance()->getDebug()) {
                $response['details'] = $e->getTrace();
            }

            return $response;
        }
    }
}
