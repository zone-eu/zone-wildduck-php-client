<?php

namespace Zone\Wildduck;

use ErrorException;
use Override;
use Zone\Wildduck\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\UnexpectedValueException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\Util\RequestOptions;
use Zone\Wildduck\Util\Util;

use function is_countable;

class BaseWildduckClient implements WildduckClientInterface
{
    private const array DEFAULT_CONFIG = [
        'access_token' => null,
        'api_base' => 'https://localhost:8080',
        'resolve_uri' => false,
        'session' => null,
        'ip' => null,
    ];

    protected static null|WildduckClient $_instance = null;

    /** @var array<string, mixed> */
    private array $config;

    private readonly RequestOptions $defaultOpts;

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
	public function __construct(array|string $config = [])
    {
        if (!is_array($config)) {
            throw new InvalidArgumentException('$config must be an array');
        }

        $config = array_merge(self::DEFAULT_CONFIG, $config);
        $this->validateConfig($config);

        $this->config = $config;

        $this->defaultOpts = RequestOptions::parse([]);
    }

    public static function instance(array|string $config = []): WildduckClient
    {
        if (!self::$_instance instanceof WildduckClient) {
            self::$_instance = new WildduckClient($config);
        }

        if (is_countable($config) && $config !== []) {
            self::$_instance->validateConfig($config, array_keys($config) === ['access_token']);
            self::$_instance->updateConfig($config);
        }

        return WildduckClient::$_instance;
    }

    public static function token(string $token): BaseWildduckClient
    {
        return self::instance(['access_token' => $token]);
    }

    public function resolve(): static
    {
        $this->config['resolve_uri'] = true;
        return $this;
    }

    /**
     * Gets the access token used by the client to send requests.
     *
     * @return null|string the access token used by the client to send requests
     */
    #[Override]
    public function getAccessToken(): string|null
    {
        return $this->config['access_token'];
    }

    /**
     * Gets the base URL for Wildduck's API.
     *
     * @return string the base URL for Wildduck's API
     */
    #[Override]
    public function getApiBase(): string
    {
        return $this->config['api_base'];
    }

    /**
     * Sends a request to Wildduck's API.
     *
     * @param string $method the HTTP method
     * @param string $path the path of the request
     * @param array|null $params the parameters of the request
     * @param array|null $opts the special modifiers of the request
     * @param bool $fileUpload
     *
     * @return mixed the object returned by Wildduck's API
     *
     * @throws ApiConnectionException
     * @throws UnexpectedValueException
     * @throws AuthenticationFailedException
     * @throws RequestFailedException
     * @throws ValidationException
     * @throws InvalidAccessTokenException
     * @throws InvalidDatabaseException
     */
    #[Override]
    public function request(string $method, string $path, array|null $params, array|RequestOptions|null $opts, bool $fileUpload = false): mixed
    {
        if ($this->config['resolve_uri']) {
            return $path;
        }

        if ($fileUpload) {
            $path = $this->setIdentificationPath($path);
        } else {
            $params = $this->setIdentificationParams($params);
        }

	    $opts = $this->defaultOpts->merge($opts, true);
	    $baseUrl = $opts->apiBase ?: $this->getApiBase();
        $requestor = new ApiRequestor($this->accessTokenForRequest($opts), $baseUrl);

	    [$response, $opts->apiKey] = $requestor->request($method, $path, $params, $opts->headers, $opts->raw, $fileUpload);


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
     * @param array|null $params the parameters of the request
     * @param array|RequestOptions|null $opts the special modifiers of the request
     *
     * @return Collection2 of ApiResources
     *
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws InvalidDatabaseException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function requestCollection(string $method, string $path, array|null $params, array|RequestOptions|null $opts): Collection2
    {
        $obj = $this->request($method, $path, $params, $opts);
		if (!($obj instanceof Collection2)) {
            $received_class = $obj::class;
            $msg = sprintf('Expected to receive `Zone\Wildduck\Collection2` object from Wildduck API. Instead received `%s`.', $received_class);

            throw new UnexpectedValueException($msg);
        }

        $obj->setFilters($params);

        return $obj;
    }

	/**
	 * @throws ErrorException
	 */
	public function stream(string $method, string $path, array|null $params, array|object|null $opts): StreamedResponse
    {
        $baseUrl = $opts->apiBase ?? $this->getApiBase();
        return (new StreamRequest($baseUrl, $this->accessTokenForRequest($opts)))->stream($method, $path, $params, $opts->headers ?? []);
    }

    /**
     * @param RequestOptions|array|null $opts
     *
     * @return null|string
     */
    private function accessTokenForRequest(RequestOptions|array|null $opts): string|null
    {
        return $opts->accessToken ?? $this->getAccessToken();
    }

    /**
     * @param array<string, mixed> $config
     *
     * @throws InvalidArgumentException
     */
    protected function validateConfig(array $config, bool $tokenOnly = false): void
    {
        // access_token
        if (null !== $config['access_token'] && !is_string($config['access_token'])) {
            throw new InvalidArgumentException('access_token must be null or a string');
        }

        if ('' === $config['access_token']) {
            $msg = 'access_token cannot be an empty string';

            throw new InvalidArgumentException($msg);
        }

        if ($tokenOnly) {
            return;
        }

        // api_base
        if (!is_string($config['api_base'])) {
            throw new InvalidArgumentException('api_base must be a string');
        }

        // check absence of extra keys
        $extraConfigKeys = array_diff(array_keys($config), array_keys(self::DEFAULT_CONFIG));
        if ($extraConfigKeys !== []) {
            throw new InvalidArgumentException('Found unknown key(s) in configuration array: ' . implode(',', $extraConfigKeys));
        }
    }

    protected function updateConfig(string|array $config): void
    {
        foreach ($config as $k => $v) {
            $this->config[$k] = $v;
        }
    }

    private function setIdentificationPath(string $path): string
    {
        $prefix = '?';
        if (str_contains($path, '?')) {
            $prefix = '&';
        }

        if ($this->config['session']) {
            $path = sprintf('%s%ssess=%s', $path, $prefix, $this->config['session']);
            $prefix = '&';
        }

        if ($this->config['ip']) {
            return sprintf('%s%sip=%s', $path, $prefix, $this->config['ip']);
        }

        return $path;
    }

    /**
     * @param array|null $params
     * @return array
     */
    private function setIdentificationParams(array|null $params): array
    {
        if ($this->config['session']) {
            $params['sess'] = $this->config['session'];
        }

        if ($this->config['ip']) {
            $params['ip'] = $this->config['ip'];
        }

        return $params;
    }
}
