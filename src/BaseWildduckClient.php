<?php

namespace Zone\Wildduck;

use ErrorException;
use Override;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidArgumentException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\Util\RequestOptions;

use function in_array;

class BaseWildduckClient implements WildduckClientInterface
{
    private const array DEFAULT_CONFIG = [
        'access_token' => null,
        'auth_mode' => ApiRequestor::AUTH_MODE_ACCESS_TOKEN,
        'api_base' => 'https://localhost:8080',
        'resolve_uri' => false,
        'session' => null,
        'ip' => null,
        'request_options' => [],
    ];

    /** @var array<string, mixed> */
    private array $config;

    private readonly RequestOptions $defaultOpts;

    /**
     * Initializes a new instance of the {@link BaseWildduckClient} class.
     *
     * The constructor takes a single argument: an array with the client configuration
     * settings. A string argument is rejected.
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
     * @param array<string, mixed>|string $config the client configuration settings
     */
    public function __construct(array|string $config = [])
    {
        if (!is_array($config)) {
            throw new InvalidArgumentException('$config must be an array');
        }

        $config = array_merge(self::DEFAULT_CONFIG, $config);
        $this->validateConfig($config);

        $this->config = $config;

        // @phpstan-ignore function.alreadyNarrowedType
        $this->defaultOpts = RequestOptions::parse(array_key_exists('request_options', $config) ? $config['request_options'] : []);
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

        // auth_mode
        if (($config['auth_mode'] ?? null) !== null
            && !in_array($config['auth_mode'], [ApiRequestor::AUTH_MODE_ACCESS_TOKEN, ApiRequestor::AUTH_MODE_BEARER], true)
        ) {
            throw new InvalidArgumentException('auth_mode must be "access_token" or "bearer"');
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

    public static function token(string $token): BaseWildduckClient
    {
        return self::instance(['access_token' => $token, 'auth_mode' => ApiRequestor::AUTH_MODE_ACCESS_TOKEN]);
    }

    /**
     * Returns a client that presents its token as a bearer credential
     * (Authorization: Bearer) instead of the X-Access-Token header.
     * Required for MCP (wdmcp_) credentials, which WildDuck refuses
     * in the header carrier.
     */
    public static function bearerToken(string $token): BaseWildduckClient
    {
        return self::instance(['access_token' => $token, 'auth_mode' => ApiRequestor::AUTH_MODE_BEARER]);
    }

    public static function instance(array|string $config = []): WildduckClient
    {
        return new WildduckClient($config);
    }

    public function resolve(): static
    {
        $this->config['resolve_uri'] = true;
        return $this;
    }

    /**
     * Sends a request to Wildduck's API.
     *
     * @param string $method The HTTP method being used
     * @param string $path The URL being requested, including domain and protocol
     * @param mixed $params Must be KV pairs when not uploading files otherwise anything is allowed, string is expected for file upload. Can be nested for arrays and hashes
     * @param array|RequestOptions|null $opts the special modifiers of the request
     * @param bool $fileUpload
     *
     * @return ApiResponse|string the object returned by Wildduck's API
     *
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    #[Override]
    public function request(string $method, string $path, mixed $params, array|RequestOptions|null $opts, bool $fileUpload = false): ApiResponse|string
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
        $requestor = new ApiRequestor(
            $this->accessTokenForRequest($opts),
            $baseUrl,
            $this->config['auth_mode']
        );


        [$response, $opts->apiKey] = $requestor->request($method, $path, $params, $opts->headers, $opts->raw, $fileUpload);

        $opts->discardNonPersistentHeaders();

        return $response;
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
        $params = $params ?? [];

        if ($this->config['session']) {
            $params['sess'] = $this->config['session'];
        }

        if ($this->config['ip']) {
            $params['ip'] = $this->config['ip'];
        }

        return $params;
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
     * @param RequestOptions|array|null $opts
     *
     * @return null|string
     */
    private function accessTokenForRequest(RequestOptions|array|null $opts): string|null
    {
        return $opts->accessToken ?? $this->getAccessToken();
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

    public function stream(string $method, string $path, array|null $params, array|object|null $opts): StreamedResponse
    {
        $baseUrl = $opts->apiBase ?? $this->getApiBase();
        $streamRequest = new StreamRequest($baseUrl, $this->accessTokenForRequest($opts), [], $this->config['auth_mode']);

        return $streamRequest->stream($method, $path, $params, $opts->headers ?? []);
    }
}
