<?php

namespace Wildduck\Http;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Request
 * @package Wildduck\Http
 */
class Request
{

    /**
     * @param string $uri
     * @param array $params
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function get(string $uri, array $params = []) : ResponseInterface
    {
        return self::request('get', $uri, $params);
    }

    /**
     * @param string $uri
     * @param array $params
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function post(string $uri, array $params = []) : ResponseInterface
    {
        return self::request('post', $uri, $params);
    }

    /**
     * @param string $uri
     * @param array $params
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function put(string $uri, array $params = []) : ResponseInterface
    {
        return self::request('put', $uri, $params);
    }

    /**
     * @param string $uri
     * @param array $params
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function delete(string $uri, array $params = []) : ResponseInterface
    {
        return self::request('delete', $uri, $params);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $params
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function request(string $method, string $uri, array $params = []) : ResponseInterface
    {
        $client = new Client([
            'base_uri' => config('wildduck.host'),
            'timeout' => 2.0,
            'json' => true,
        ]);
        
        $opts = [];
        
        if (count($params)) {
            if ($method === 'get') {
                $opts = ['query' => $params];
            } else {
                $opts = ['body' => $params];
            }
        }

        return $client->request($method, $uri, $opts);
    }
}
