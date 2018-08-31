<?php

namespace Wildduck\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Wildduck\Client as WildduckClient;
use Wildduck\Exceptions\RequestFailedException;

class Request
{
    const HTTP_OK = 0;
    const HTTP_ERROR = 1;

    /**
     * @param string $uri
     * @param array $params
     * @return array
     * @throws RequestFailedException
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
            return json_decode($res->getBody()->getContents(), true);
        } catch (BadResponseException $e) {
            throw $e;
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
