<?php

namespace Zone\Wildduck;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Zone\Wildduck\Util\Event;

class StreamRequest
{
    const HTTP_ERROR = 1;
    const RETRY_DEFAULT_MS = 3000;
    const END_OF_MESSAGE = "/\r\n\r\n|\n\n|\r\r/";

    private static ?Client $_httpClient = null;
    private static array $_httpOptions = [];

    private static Request $_request;
    private static ResponseInterface $_response;

    private ?string $_apiBase;
    private ?string $_accessToken;
    private ?string $_lastId = null;
    private ?int $_retry = self::RETRY_DEFAULT_MS;

    public function __construct($apiBase, $accessToken, $opts = [])
    {
        if (!$apiBase) {
            $apiBase = Wildduck::getApiBase();
        }

        if (!$accessToken) {
            $accessToken = Wildduck::getAccessToken();
        }

        $this->_apiBase = $apiBase;
        $this->_accessToken = $accessToken;
        self::$_request = Request::createFromGlobals();

        if ($opts) {
            self::$_httpOptions = $opts;
        }

        self::$_httpClient = new Client([
            'base_uri' => $this->_apiBase,
            'headers' => [
                'Accept' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'X-Access-Token' => $this->_accessToken,
            ]
        ]);
    }

    public function stream(string $method, string $path, ?array $params = [], ?array $headers = [])
    {
        $params ??= [];
        $headers ??= [];

        $this->init();
        $this->connect($method, $path, $headers);

        $callback = function () use ($method, $path, $headers) {
            $buffer = '';
            $body = self::$_response->getBody();

            while (true) {
                if ($body->eof()) {
                    sleep($this->_retry / 1000);
                    $this->connect($method, $path, $headers);
                    $buffer = '';
                }

                $buffer .= $body->read(1);
                if (preg_match(self::END_OF_MESSAGE, $buffer)) {
                    $parts = preg_split(self::END_OF_MESSAGE, $buffer, 2);

                    $rawMessage = $parts[0];
                    $remaining = $parts[1];

                    $buffer = $remaining;
                    $event = Event::parse($rawMessage);

                    if ($event->getId()) {
                        $this->_lastId = $event->getId();
                    }

                    if ($event->getRetry()) {
                        $this->_retry = $event->getRetry();
                    }

                    echo $rawMessage . "\n\n";
                    @ob_flush();
                    @flush();
                } else {
                    @ob_flush();
                    @flush();

                    if (connection_aborted()) {
                        break;
                    }
                }
            }
        };

        $response = new StreamedResponse($callback, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no', // Disable FastCGI Buffering on Nginx
            'Transfer-Encoding' => 'identity',

            // CORS - TODO: Make configurable
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Headers' => '*',
            'Access-Control-Allow-Methods' => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);

        return $response;
    }

    private function init()
    {
        @set_time_limit(0); // Disable time limit

        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', 1);
        }

        @ini_set('output_buffering', 0);
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);

        while (ob_get_level() != 0) {
            ob_end_flush();
        }

        ob_implicit_flush(1);
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $headers
     * @throws \ErrorException
     */
    private function connect(string $method, string $path, array $headers = [])
    {
        if ($this->_lastId) {
            $headers['Last-Event-ID'] = $this->_lastId;
        }

        try {
            self::$_response = self::$_httpClient->request($method, $path, [
                'stream' => true,
                'headers' => $headers,
            ]);

            if (self::$_response->getStatusCode() === 204) {
                // TODO: Probably better structured response
                throw new \RuntimeException('Access denied');
            }
        } catch (GuzzleException $e) {
            $message = $e->getMessage();
            throw new \ErrorException("$method - $message", self::HTTP_ERROR);
        }
    }
}