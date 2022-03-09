<?php

namespace Zone\Wildduck;

use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\UnexpectedValueException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\Util\RequestOptions;
use Zone\Wildduck\Util\Util;

class BaseWildduckClient implements WildduckClientInterface
{

    private const DEFAULT_CONFIG = [
        'access_token' => null,
        'api_base' => 'https://localhost:8080',
        'resolve_uri' => false,
        'session' => null,
        'ip' => null,
    ];

    private static $_instance = null;

    /** @var array<string, mixed> */
    private $config;

    /** @var RequestOptions */
    private $defaultOpts;

    /**
     * Initializes a new instance of the {@link BaseWildduckClient} class.
     *
     * The constructor takes a single argument. The argument can be a string, in which case it
     * should be the API key. It can also be an array with various configuration settings.
     *
     * Configuration settings include the following options:
     *
     * - access_token (null|string): the Wildduck API global access token, to be used in regular API requests.
     * If available, the user_token is used by the system unless explicitly defined not to do so.
     *
     * The following configuration settings are also available, though setting these should rarely be necessary
     * (only useful if you want to send requests to a mock server like stripe-mock):
     *
     * - api_base (string): the base URL for regular API requests. Defaults to
     *   {@link DEFAULT_CONFIG}.
     *
     * @param array<string, mixed>|string $config the API key as a string, or an array containing
     *   the client configuration settings
     */
    public function __construct($config = [])
    {
        if (!\is_array($config)) {
            throw new \Zone\Wildduck\Exception\InvalidArgumentException('$config must be an array');
        }

        $config = \array_merge(self::DEFAULT_CONFIG, $config);
        $this->validateConfig($config);

        $this->config = $config;

        $this->defaultOpts = RequestOptions::parse([]);
    }

    public static function instance($config = [])
    {
        if (null === self::$_instance) {
            self::$_instance = new static($config);
        }

        if (count($config)) {
            self::$_instance->validateConfig($config, array_keys($config) === ['access_token']);
            self::$_instance->updateConfig($config);
        }

        return self::$_instance;
    }

    public static function token($token)
    {
        return self::instance(['access_token' => $token]);
    }

    public function resolve()
    {
        $this->config['resolve_uri'] = true;
        return $this;
    }

    /**
     * Gets the access token used by the client to send requests.
     *
     * @return null|string the access token used by the client to send requests
     */
    public function getAccessToken()
    {
        return $this->config['access_token'];
    }

    /**
     * Gets the base URL for Wildduck's API.
     *
     * @return string the base URL for Wildduck's API
     */
    public function getApiBase()
    {
        return $this->config['api_base'];
    }

    /**
     * Sends a request to Wildduck's API.
     *
     * @param string $method the HTTP method
     * @param string $path the path of the request
     * @param array $params the parameters of the request
     * @param array|RequestOptions $opts the special modifiers of the request
     * @param bool $fileUpload
     *
     * @return WildduckObject|string the object returned by Wildduck's API
     *
     * @throws ApiConnectionException
     * @throws UnexpectedValueException
     * @throws AuthenticationFailedException
     * @throws RequestFailedException
     * @throws ValidationException
     * @throws InvalidAccessTokenException
     * @throws InvalidDatabaseException
     */
    public function request($method, $path, $params, $opts, $fileUpload = false)
    {
        if ($this->config['resolve_uri']) return $path;

        $opts = $this->defaultOpts->merge($opts, true);

        if ($fileUpload) {
            $path = $this->setIdentificationPath($path);
        } else {
            $params = $this->setIdentificationParams($params);
        }

        $baseUrl = $opts->apiBase ?: $this->getApiBase();


        $requestor = new ApiRequestor($this->accessTokenForRequest($opts), $baseUrl);
        list($response, $opts->apiKey) = $requestor->request($method, $path, $params, $opts->headers, $opts->raw, $fileUpload);
        $opts->discardNonPersistentHeaders();

        if ($opts->raw) {
            return $response;
        }

        $obj = Util::convertToWildduckObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }

    /**
     * Sends a request to Wildduck's API.
     *
     * @param string $method the HTTP method
     * @param string $path the path of the request
     * @param array $params the parameters of the request
     * @param array|RequestOptions $opts the special modifiers of the request
     *
     * @return Collection2 of ApiResources
     *
     * @throws ApiConnectionException
     * @throws UnexpectedValueException
     * @throws AuthenticationFailedException
     * @throws RequestFailedException
     * @throws ValidationException
     * @throws InvalidAccessTokenException
     */
    public function requestCollection($method, $path, $params, $opts)
    {
        $obj = $this->request($method, $path, $params, $opts);
        if (!($obj instanceof Collection2)) {
            $received_class = \get_class($obj);
            $msg = "Expected to receive `Zone\Wildduck\Collection2` object from Wildduck API. Instead received `{$received_class}`.";

            throw new UnexpectedValueException($msg);
        }
        $obj->setFilters($params);

        return $obj;
    }

    public function stream(string $method, string $path, $params, $opts)
    {
        $baseUrl = $opts->apiBase ?? $this->getApiBase();
        $requestor = new StreamRequest($baseUrl, $this->accessTokenForRequest($opts));
        return $requestor->stream($method, $path, $params, $opts->headers ?? []);
    }

    /**
     * @param RequestOptions $opts
     *
     * @return string
     */
    private function accessTokenForRequest($opts)
    {
        return $opts->accessToken ?? $this->getAccessToken();
    }

    /**
     * @param array<string, mixed> $config
     * @param bool $tokenOnly
     *
     * @throws \Zone\Wildduck\Exception\InvalidArgumentException
     */
    private function validateConfig($config, $tokenOnly = false)
    {
        // access_token
        if (null !== $config['access_token'] && !\is_string($config['access_token'])) {
            throw new \Zone\Wildduck\Exception\InvalidArgumentException('access_token must be null or a string');
        }

        if (null !== $config['access_token'] && ('' === $config['access_token'])) {
            $msg = 'access_token cannot be an empty string';

            throw new \Zone\Wildduck\Exception\InvalidArgumentException($msg);
        }

        if ($tokenOnly) return;

        // api_base
        if (!\is_string($config['api_base'])) {
            throw new \Zone\Wildduck\Exception\InvalidArgumentException('api_base must be a string');
        }

        // check absence of extra keys
        $extraConfigKeys = \array_diff(\array_keys($config), \array_keys(self::DEFAULT_CONFIG));
        if (!empty($extraConfigKeys)) {
            throw new \Zone\Wildduck\Exception\InvalidArgumentException('Found unknown key(s) in configuration array: ' . \implode(',', $extraConfigKeys));
        }
    }

    private function updateConfig($config): void {
        foreach ($config as $k => $v) {
            $this->config[$k] = $v;
        }
    }

    private function setIdentificationPath($path)
    {
        $prefix = '?';
        if (strpos($path, '?') !== false) {
            $prefix = '&';
        }

        if ($this->config['session']) {
            $path = sprintf('%s%ssess=%s', $path, $prefix, $this->config['session']);
            $prefix = '&';
        }

        if ($this->config['ip']) {
            $path = sprintf('%s%sip=%s', $path, $prefix, $this->config['ip']);
        }

        return $path;
    }

    private function setIdentificationParams($params)
    {
        if ($this->config['session']) $params['sess'] = $this->config['session'];
        if ($this->config['ip']) $params['ip'] = $this->config['ip'];
        return $params;
    }

}































