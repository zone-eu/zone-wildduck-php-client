<?php

namespace Zone\Wildduck;

use ErrorException;
use RuntimeException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Zone\Wildduck\Util\Event;
use Zone\Wildduck\Util\RequestOptions;

class StreamRequest
{
    public const int HTTP_ERROR = 1;

    public const int RETRY_DEFAULT_MS = 3000;

    public const string END_OF_MESSAGE = "/\r\n\r\n|\n\n|\r\r/";

    private static ?Client $_httpClient = null;

    private static array $_httpOptions = [];

    private static Request $_request;

    private static ResponseInterface $_response;

    private string|null $_lastId = null;

    private int|null $_retry = self::RETRY_DEFAULT_MS;

    public function __construct(string|null $apiBase, string|null $accessToken, array|RequestOptions $opts = [])
    {

        if (!$apiBase) {
	        $apiBase = Wildduck::getApiBase();
        }

        if (!$accessToken) {
	        $accessToken = Wildduck::getAccessToken();
        }

        self::$_request = Request::createFromGlobals();

        if ($opts) {
            self::$_httpOptions = $opts;
        }

        self::$_httpClient = new Client([
            'base_uri' => $apiBase,
            'headers' => [
                'Accept' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'X-Access-Token' => $accessToken,
            ]
        ]);
    }

	/**
	 * @param string $method
	 * @param string $path
	 * @param array|null $params
	 * @param array|null $headers
	 * @return StreamedResponse
	 * @throws ErrorException
	 */
    public function stream(string $method, string $path, array|null $params = [], array|null $headers = []): StreamedResponse
    {
        $params ??= [];
        $headers ??= [];


        $min_ob_level = (int) $params['min_ob_level'] ?: 0;
        unset($params['min_ob_level']);

        $this->init($min_ob_level);
        $this->connect($method, $path, $headers);

        $callback = function () use ($method, $path, $headers, $params): void {
            $buffer = '';
            $body = self::$_response->getBody();

            while (true) {
                if ($body->eof()) {
                    sleep($this->_retry / 1000);
                    $this->connect($method, $path, $headers);
                    $buffer = '';
                    $body = self::$_response->getBody();
                }

                if (is_callable($params['isDisconnectedCallback']) && call_user_func($params['isDisconnectedCallback'])) {
                    $body->close();
                    break;
                }

                $buffer .= $body->read(1);
                if (preg_match(self::END_OF_MESSAGE, $buffer)) {
                    $parts = preg_split(self::END_OF_MESSAGE, $buffer, 2);

                    [$rawMessage, $remaining] = $parts;

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

                    if (connection_aborted() !== 0) {
                        $body->close();
                        break;
                    }
                }
            }
        };

        return new StreamedResponse($callback, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no', // Disable FastCGI Buffering on Nginx
            'Transfer-Encoding' => 'chunked',

            // CORS - TODO: Make configurable
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Headers' => '*',
            'Access-Control-Allow-Methods' => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);
    }

    private function init(int $min_ob_level): void
    {
        @set_time_limit(0); // Disable time limit

        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', 1);
        }

        @ini_set('output_buffering', 0);
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);

        while (ob_get_level() > $min_ob_level) {
            ob_end_flush();
        }

        ob_implicit_flush(1);
    }

    /**
     * @throws ErrorException
     */
    private function connect(string $method, string $path, array $headers = []): void
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
                throw new RuntimeException('Access denied');
            }
        } catch (GuzzleException $guzzleException) {
            $message = $guzzleException->getMessage();
            throw new ErrorException(sprintf('%s - %s', $method, $message), self::HTTP_ERROR, $guzzleException);
        }
    }
}
