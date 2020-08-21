<?php

namespace Zone\Wildduck;

class BaseWildduckClient implements WildduckClientInterface
{
    /** @var string default base URL for Wildduck API */
    const DEFAULT_API_BASE = 'https://localhost:8080';

    private static $_instance = null;

    /** @var array<string, mixed> */
    private $config;

    /** @var \Zone\Wildduck\Util\RequestOptions */
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
     *   {@link DEFAULT_API_BASE}.
     *
     * @param array<string, mixed>|string $config the API key as a string, or an array containing
     *   the client configuration settings
     */
    public function __construct($config = [])
    {
        if (!\is_array($config)) {
            throw new \Zone\Wildduck\Exception\InvalidArgumentException('$config must be an array');
        }

        $config = \array_merge($this->getDefaultConfig(), $config);
        $this->validateConfig($config);

        $this->config = $config;

        $this->defaultOpts = \Zone\Wildduck\Util\RequestOptions::parse([]);
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
     * @param array|\Zone\Wildduck\Util\RequestOptions $opts the special modifiers of the request
     *
     * @return \Zone\Wildduck\WildduckObject the object returned by Wildduck's API
     */
    public function request($method, $path, $params, $opts)
    {
        $opts = $this->defaultOpts->merge($opts, true);
        $baseUrl = $opts->apiBase ?: $this->getApiBase();
        $requestor = new \Zone\Wildduck\ApiRequestor($this->accessTokenForRequest($opts), $baseUrl);
        list($response, $opts->apiKey) = $requestor->request($method, $path, $params, $opts->headers, $opts->raw);
        $opts->discardNonPersistentHeaders();

        if ($opts->raw) {
            return $response;
        }

        $obj = \Zone\Wildduck\Util\Util::convertToWildduckObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }

    /**
     * Sends a request to Wildduck's API.
     *
     * @param string $method the HTTP method
     * @param string $path the path of the request
     * @param array $params the parameters of the request
     * @param array|\Zone\Wildduck\Util\RequestOptions $opts the special modifiers of the request
     *
     * @return \Zone\Wildduck\Collection of ApiResources
     */
    public function requestCollection($method, $path, $params, $opts)
    {
        $obj = $this->request($method, $path, $params, $opts);
        if (!($obj instanceof \Zone\Wildduck\Collection2)) {
            $received_class = \get_class($obj);
            $msg = "Expected to receive `Zone\Wildduck\\Collection2` object from Wildduck API. Instead received `{$received_class}`.";

            throw new \Zone\Wildduck\Exception\UnexpectedValueException($msg);
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
     * @param \Zone\Wildduck\Util\RequestOptions $opts
     *
     * @return string
     */
    private function accessTokenForRequest($opts)
    {
        return $opts->accessToken ?? $this->getAccessToken();
    }

    /**
     * TODO: replace this with a private constant when we drop support for PHP < 5.
     *
     * @return array<string, mixed>
     */
    private function getDefaultConfig()
    {
        return [
            'access_token' => null,
            'api_base' => self::DEFAULT_API_BASE,
        ];
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
        $extraConfigKeys = \array_diff(\array_keys($config), \array_keys($this->getDefaultConfig()));
        if (!empty($extraConfigKeys)) {
            throw new \Zone\Wildduck\Exception\InvalidArgumentException('Found unknown key(s) in configuration array: ' . \implode(',', $extraConfigKeys));
        }
    }

    private function updateConfig($config): void {
        foreach ($config as $k => $v) {
            $this->config[$k] = $v;
        }
    }
}
