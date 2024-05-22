<?php

namespace Zone\Wildduck\HttpClient;

use CurlHandle;
use AllowDynamicProperties;
use Override;
use Zone\Wildduck\Util\RandomGenerator;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\UnexpectedValueException;
use Zone\Wildduck\Util\CaseInsensitiveArray;
use Zone\Wildduck\Wildduck;
use Zone\Wildduck\Util;

// @codingStandardsIgnoreStart
// PSR2 requires all constants be upper case. Sadly, the CURL_SSLVERSION
// constants do not abide by those rules.

// Note the values come from their position in the enums that
// defines them in cURL's source code.

// Available since PHP 5.5.19 and 5.6.3
if (!defined('CURL_SSLVERSION_TLSv1_2')) {
    define('CURL_SSLVERSION_TLSv1_2', 6);
}

// @codingStandardsIgnoreEnd

// Available since PHP 7.0.7 and cURL 7.47.0
if (!defined('CURL_HTTP_VERSION_2TLS')) {
    define('CURL_HTTP_VERSION_2TLS', 4);
}

#[AllowDynamicProperties]
class CurlClient implements ClientInterface
{
    private static ?CurlClient $instance = null;

    public static function instance(): CurlClient
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private readonly RandomGenerator $randomGenerator;

    private array $userAgentInfo;

    private bool $enablePersistentConnections = true;

    private bool $enableHttp2;

    private CurlHandle|bool|null $curlHandle = null;

    private $requestStatusCallback;

    /**
     * CurlClient constructor.
     *
     * Pass in a callable to $defaultOptions that returns an array of CURLOPT_* values to start
     * off a request with, or a flat array with the same format used by curl_setopt_array() to
     * provide a static set of options. Note that many options are overridden later in the request
     * call, including timeouts, which can be set via setTimeout() and setConnectTimeout().
     *
     * Note that request() will silently ignore a non-callable, non-array $defaultOptions, and will
     * throw an exception if $defaultOptions returns a non-array value.
     */
    public function __construct(private readonly array|null $defaultOptions = null, null|RandomGenerator $randomGenerator = null)
    {
        $this->randomGenerator = $randomGenerator ?: new RandomGenerator();
        $this->initUserAgentInfo();

        $this->enableHttp2 = $this->canSafelyUseHttp2();
    }

    public function __destruct()
    {
        $this->closeCurlHandle();
    }

    public function initUserAgentInfo(): void
    {
        $curlVersion = curl_version();
        $this->userAgentInfo = [
            'httplib' => 'curl ' . $curlVersion['version'],
            'ssllib' => $curlVersion['ssl_version'],
        ];
    }

    public function getDefaultOptions(): array|null
    {
        return $this->defaultOptions;
    }

    public function getUserAgentInfo(): array
    {
        return $this->userAgentInfo;
    }

    /**
     * @return bool
     */
    public function getEnablePersistentConnections(): bool
    {
        return $this->enablePersistentConnections;
    }

    /**
     * @param bool $enable
     */
    public function setEnablePersistentConnections(bool $enable): void
    {
        $this->enablePersistentConnections = $enable;
    }

    public function getEnableHttp2(): bool
    {
        return $this->enableHttp2;
    }

    public function setEnableHttp2(bool $enable): void
    {
        $this->enableHttp2 = $enable;
    }

    /**
     * @return null|callable
     */
    public function getRequestStatusCallback(): null|callable
    {
        return $this->requestStatusCallback;
    }

    /**
     * Sets a callback that is called after each request. The callback will
     * receive the following parameters:
     * <ol>
     *   <li>string $rbody The response body</li>
     *   <li>integer $rcode The response status code</li>
     *   <li>\Zone\Wildduck\Util\CaseInsensitiveArray $rheaders The response headers</li>
     *   <li>integer $errno The curl error number</li>
     *   <li>string|null $message The curl error message</li>
     *   <li>boolean $shouldRetry Whether the request will be retried</li>
     *   <li>integer $numRetries The number of the retry attempt</li>
     * </ol>.
     *
     * @param null|callable $requestStatusCallback
     */
    public function setRequestStatusCallback(null|callable $requestStatusCallback): void
    {
        $this->requestStatusCallback = $requestStatusCallback;
    }

    // USER DEFINED TIMEOUTS

    public const int DEFAULT_TIMEOUT = 80;

    public const int DEFAULT_CONNECT_TIMEOUT = 30;

    private int $timeout = self::DEFAULT_TIMEOUT;

    private int $connectTimeout = self::DEFAULT_CONNECT_TIMEOUT;

    public function setTimeout(int $seconds): static
    {
        $this->timeout = max($seconds, 0);

        return $this;
    }

    public function setConnectTimeout(int $seconds): static
    {
        $this->connectTimeout = max($seconds, 0);

        return $this;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function getConnectTimeout(): int
    {
        return $this->connectTimeout;
    }

    /**
     * @param string $method
     * @param string $absUrl
     * @param array $headers
     * @param array $params
     * @param bool $hasFile
     * @param bool $fileUpload
     *
     * @return array
     * @throws ApiConnectionException
     */
    #[Override]
    public function request(string $method, string $absUrl, array $headers, array $params, bool $hasFile, bool $fileUpload = false): array
    {
        $method = strtolower($method);

        $opts = [];
        if (is_callable($this->defaultOptions)) { // call defaultOptions callback, set options to return value
            $opts = call_user_func_array($this->defaultOptions, func_get_args());
            if (!is_array($opts)) {
                throw new UnexpectedValueException('Non-array value returned by defaultOptions CurlClient callback');
            }
        } elseif (is_array($this->defaultOptions)) { // set default curlopts from array
            $opts = $this->defaultOptions;
        }

        $params = Util\Util::objectsToIds($params);

        if ('get' === $method) {
            if (isset($params['sess'])) {
                unset($params['sess']);
            }

            if (isset($params['ip'])) {
                unset($params['ip']);
            }

            if ($hasFile) {
                throw new UnexpectedValueException(
                    'Issuing a GET request with a file parameter'
                );
            }

            $opts[CURLOPT_HTTPGET] = 1;
            if (is_countable($params) && count($params) > 0) {
                $encoded = Util\Util::encodeParameters($params);
                $absUrl = sprintf('%s?%s', $absUrl, $encoded);
            }
        } elseif ('post' === $method) {
            if (!$fileUpload && !count($params)) {
                $params = [];
            }

            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $fileUpload || $hasFile ? $params : json_encode($params);
        } elseif ('put' === $method) {
            if (is_countable($params) && count($params) === 0) {
                $params = [];
            }

            $opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
            $opts[CURLOPT_POSTFIELDS] = $hasFile ? $params : json_encode($params);
        } elseif ('delete' === $method) {
            $opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
            if (is_countable($params) && count($params) > 0) {
                $encoded = http_build_query($params);
                $absUrl = sprintf('%s?%s', $absUrl, $encoded);
            }
        } else {
            throw new UnexpectedValueException('Unrecognized method ' . $method);
        }

        // It is only safe to retry network failures on POST requests if we
        // add an Idempotency-Key header
        if ('post' === $method && Wildduck::$maxNetworkRetries > 0 && !$this->hasHeader($headers, 'Idempotency-Key')) {
            $headers[] = 'Idempotency-Key: ' . $this->randomGenerator->uuid();
        }

        // By default, for large request body sizes (> 1024 bytes), cURL will
        // send a request without a body and with a `Expect: 100-continue`
        // header, which gives the server a chance to respond with an error
        // status code in cases where one can be determined right away (say
        // on an authentication problem for example), and saves the "large"
        // request body from being ever sent.
        //
        // Unfortunately, the bindings don't currently correctly handle the
        // success case (in which the server sends back a 100 CONTINUE), so
        // we'll error under that condition. To compensate for that problem
        // for the time being, override cURL's behavior by simply always
        // sending an empty `Expect:` header.
        $headers[] = 'Expect: ';

        $absUrl = Util\Util::utf8($absUrl);
        $opts[CURLOPT_URL] = $absUrl;
        $opts[CURLOPT_RETURNTRANSFER] = true;
        $opts[CURLOPT_CONNECTTIMEOUT] = $this->connectTimeout;
        $opts[CURLOPT_TIMEOUT] = $this->timeout;
        $opts[CURLOPT_HTTPHEADER] = $headers;

        if (!isset($opts[CURLOPT_HTTP_VERSION]) && $this->getEnableHttp2()) {
            // For HTTPS requests, enable HTTP/2, if supported
            $opts[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_2TLS;
        }

        return $this->executeRequestWithRetries($opts, $absUrl);
    }

    /**
     * @param array $opts cURL options
     * @param string $absUrl
     * @return array
     * @throws ApiConnectionException
     */
    private function executeRequestWithRetries(array $opts, string $absUrl): array
    {
        $numRetries = 0;

        while (true) {
            $rCode = 0;
            $errno = 0;
            $message = null;

            // Create a callback to capture HTTP headers for the response
            $rHeaders = new CaseInsensitiveArray();
            $headerCallback = static function ($curl, $header_line) use (&$rHeaders): int {
                // Ignore the HTTP request line (HTTP/1.1 200 OK)
                if (!str_contains($header_line, ':')) {
                    return strlen($header_line);
                }

                [$key, $value] = explode(':', trim($header_line), 2);
                $rHeaders[trim($key)] = trim($value);
                return strlen($header_line);
            };
            $opts[CURLOPT_HEADERFUNCTION] = $headerCallback;

            $this->resetCurlHandle();
            curl_setopt_array($this->curlHandle, $opts);
            $rBody = curl_exec($this->curlHandle);

            if (false === $rBody) {
                $errno = curl_errno($this->curlHandle);
                $message = curl_error($this->curlHandle);
            } else {
                $rCode = curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE);
            }

            if (!$this->getEnablePersistentConnections()) {
                $this->closeCurlHandle();
            }

            $shouldRetry = $this->shouldRetry($errno, $rCode, $rHeaders, $numRetries);

            if (is_callable($this->getRequestStatusCallback())) {
                call_user_func($this->getRequestStatusCallback(), $rBody, $rCode, $rHeaders, $errno, $message, $shouldRetry, $numRetries);
            }

            if ($shouldRetry) {
                ++$numRetries;
                $sleepSeconds = $this->sleepTime($numRetries, $rHeaders);
                usleep($sleepSeconds * 1000000);
            } else {
                break;
            }
        }

        if (false === $rBody) {
            $this->handleCurlError($absUrl, $errno, $message, $numRetries);
        }

        return [$rBody, $rCode, $rHeaders];
    }

    /**
     * @param string $url
     * @param int $errno
     * @param string|null $message
     * @param int $numRetries
     * @throws ApiConnectionException
     */
    private function handleCurlError(string $url, int $errno, string|null $message, int $numRetries): void
    {
        switch ($errno) {
            case CURLE_COULDNT_CONNECT:
            case CURLE_COULDNT_RESOLVE_HOST:
            case CURLE_OPERATION_TIMEOUTED:
                $msg = sprintf('Could not connect to Wildduck (%s)', $url);
                break;
            case CURLE_SSL_CACERT:
            case CURLE_SSL_PEER_CERTIFICATE:
                $msg = "Could not verify Wildduck's SSL certificate";
                break;
            default:
                $msg = 'Unexpected error communicating with Wildduck.';
                $msg .= "\n\n(Network error [errno {$errno}]: {$message})";
        }

        if ($numRetries > 0) {
            $msg .= "\n\nRequest was retried {$numRetries} times.";
        }

        throw new ApiConnectionException($msg);
    }

    /**
     * Checks if an error is a problem that we should retry on. This includes both
     * socket errors that may represent an intermittent problem and some special
     * HTTP statuses.
     *
     * @param int $errno
     * @param int $rcode
     * @param array|CaseInsensitiveArray $rheaders
     * @param int $numRetries
     * @return bool
     */
    private function shouldRetry(int $errno, int $rcode, array|CaseInsensitiveArray $rheaders, int $numRetries): bool
    {
        if ($numRetries >= Wildduck::getMaxNetworkRetries()) {
            return false;
        }

        // Retry on timeout-related problems (either on open or read).
        if (CURLE_OPERATION_TIMEOUTED === $errno) {
            return true;
        }

        // Destination refused the connection, the connection was reset, or a
        // variety of other connection failures. This could occur from a single
        // saturated server, so retry in case it's intermittent.
        if (CURLE_COULDNT_CONNECT === $errno) {
            return true;
        }

        // The API may ask us not to retry (eg; if doing so would be a no-op)
        // or advise us to retry (eg; in cases of lock timeouts); we defer to that.
        if (isset($rheaders['stripe-should-retry'])) {
            if ('false' === $rheaders['stripe-should-retry']) {
                return false;
            }

            if ('true' === $rheaders['stripe-should-retry']) {
                return true;
            }
        }

        // 409 Conflict
        if (409 === $rcode) {
            return true;
        }
        // Retry on 500, 503, and other internal errors.
        //
        // Note that we expect the stripe-should-retry header to be false
        // in most cases when a 500 is returned, since our idempotency framework
        // would typically replay it anyway.
        return $rcode >= 500;
    }

    /**
     * Provides the number of seconds to wait before retrying a request.
     *
     * @param int $numRetries
     * @param array|CaseInsensitiveArray $rheaders
     * @return mixed
     */
    private function sleepTime(int $numRetries, array|CaseInsensitiveArray $rheaders): mixed
    {
        // Apply exponential backoff with $initialNetworkRetryDelay on the
        // number of $numRetries so far as inputs. Do not allow the number to exceed
        // $maxNetworkRetryDelay.
        $sleepSeconds = min(
            Wildduck::getInitialNetworkRetryDelay() * 1.0 * 2 ** ($numRetries - 1),
            Wildduck::getMaxNetworkRetryDelay()
        );

        // Apply some jitter by randomizing the value in the range of
        // ($sleepSeconds / 2) to ($sleepSeconds).
        $sleepSeconds *= 0.5 * (1 + $this->randomGenerator->randFloat());

        // But never sleep less than the base sleep seconds.
        $sleepSeconds = max(Wildduck::getInitialNetworkRetryDelay(), $sleepSeconds);

        // And never sleep less than the time the API asks us to wait, assuming it's a reasonable ask.
        $retryAfter = isset($rheaders['retry-after']) ? (float) ($rheaders['retry-after']) : 0.0;
        if (floor($retryAfter) === $retryAfter && $retryAfter <= Wildduck::getMaxRetryAfter()) {
            return max($sleepSeconds, $retryAfter);
        }

        return $sleepSeconds;
    }

    /**
     * Initializes the curl handle. If already initialized, the handle is closed first.
     */
    private function initCurlHandle(): void
    {
        $this->closeCurlHandle();
        $this->curlHandle = curl_init();
    }

    /**
     * Closes the curl handle if initialized. Do nothing if already closed.
     */
    private function closeCurlHandle(): void
    {
        if (null !== $this->curlHandle) {
            curl_close($this->curlHandle);
            $this->curlHandle = null;
        }
    }

    /**
     * Resets the curl handle. If the handle is not already initialized, or if persistent
     * connections are disabled, the handle is reinitialized instead.
     */
    private function resetCurlHandle(): void
    {
        if (null !== $this->curlHandle && $this->getEnablePersistentConnections()) {
            curl_reset($this->curlHandle);
        } else {
            $this->initCurlHandle();
        }
    }

    /**
     * Indicates whether it is safe to use HTTP/2 or not.
     */
    private function canSafelyUseHttp2(): bool
    {
        // Versions of curl older than 7.60.0 don't respect GOAWAY frames
        // (cf. https://github.com/curl/curl/issues/2416), which Wildduck use.
        $curlVersion = curl_version()['version'];

        return version_compare($curlVersion, '7.60.0') >= 0;
    }

    /**
     * Checks if a list of headers contains a specific header name.
     *
     * @param string[] $headers
     *
     */
    private function hasHeader(array $headers, string $name = ''): bool
    {
        foreach ($headers as $header) {
            if (0 === strncasecmp($header, $name . ': ', strlen($name) + 2)) {
                return true;
            }
        }

        return false;
    }
}
