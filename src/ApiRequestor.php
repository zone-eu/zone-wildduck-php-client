<?php

namespace Zone\Wildduck;

use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

/**
 * Class ApiRequestor.
 */
class ApiRequestor
{

    const CODE_INPUT_VALIDATION_ERROR = 'InputValidationError';
    const CODE_INVALID_TOKEN = 'InvalidToken';
    const CODE_AUTH_FAILED = 'AuthFailed';
    const CODE_INTERNAL_SERVER = 'InternalServer';

    /**
     * @var null|string
     */
    private ?string $_accessToken;

    /**
     * @var string
     */
    private string $_apiBase;

    /**
     * @var HttpClient\ClientInterface
     */
    private static ?HttpClient\ClientInterface $_httpClient = null;

    /**
     * ApiRequestor constructor.
     *
     * @param null|string $accessToken
     * @param null|string $apiBase
     */
    public function __construct($accessToken = null, $apiBase = null)
    {
        $this->_accessToken = $accessToken;
        if (!$apiBase) {
            $apiBase = Wildduck::$apiBase;
        }
        $this->_apiBase = $apiBase;
    }

    /**
     * @static
     *
     * @param ApiResource|array|bool|mixed $d
     *
     * @return ApiResource|array|mixed|string
     */
    private static function _encodeObjects($d)
    {
        if ($d instanceof ApiResource) {
            return Util\Util::utf8($d->id);
        }

        if (\is_array($d)) {
            $res = [];
            foreach ($d as $k => $v) {
                $res[$k] = self::_encodeObjects($v);
            }

            return $res;
        }

        return Util\Util::utf8($d);
    }

    /**
     * @param string     $method
     * @param string     $url
     * @param null|array $params
     * @param null|array $headers
     * @param bool       $raw
     *
     * @throws Exception\ApiErrorException
     *
     * @return array tuple containing (ApiResponse, API key)
     */
    public function request($method, $url, $params = null, $headers = null, $raw = false)
    {
        $params = $params ?: [];
        $headers = $headers ?: [];

        list($rbody, $rcode, $rheaders, $myApiKey) =
            $this->_requestRaw($method, $url, $params, $headers);

        $json = null;
        if (!$raw) {
            $json = $this->_interpretResponse($rbody, $rcode, $rheaders);
        }

        $resp = new ApiResponse($rbody, $rcode, $rheaders, $json);

        return [$resp, $myApiKey];
    }

    /**
     * @param string $rbody a JSON string
     * @param int $rcode
     * @param array $rheaders
     * @param array $resp
     *
     * @throws AuthenticationFailedException
     * @throws RequestFailedException
     * @throws ValidationException
     * @throws InvalidAccessTokenException
     */
    public function handleErrorResponse($rbody, $rcode, $rheaders, $resp)
    {
        if (!\is_array($resp) || (!isset($resp['error']) && !isset($resp['code']))) {
            $msg = "Invalid response object from API: {$rbody} "
              . "(HTTP response code was {$rcode})";

            throw new Exception\UnexpectedValueException($msg);
        }

        if (isset($resp['code'])) {
            self::_specificAPIError($resp['code'], $resp['message'] ?? $resp['error'] ?? 'unknown error');
        }

        throw new RequestFailedException($resp['error']);
    }

    /**
     * @static
     *
     * @param $code
     * @param $error
     * @throws AuthenticationFailedException
     * @throws RequestFailedException
     * @throws ValidationException
     * @throws InvalidAccessTokenException
     */
    private static function _specificAPIError($code, $error)
    {
        switch ($code) {
            case static::CODE_INVALID_TOKEN:
                throw new InvalidAccessTokenException('Access token used for request was invalid');
            case static::CODE_AUTH_FAILED:
                throw new AuthenticationFailedException($error);
            case static::CODE_INPUT_VALIDATION_ERROR:
                throw new ValidationException($error);
        }

        throw new RequestFailedException($error, $code);
    }

    /**
     * @static
     *
     * @param null|array $appInfo
     *
     * @return null|string
     */
    private static function _formatAppInfo($appInfo)
    {
        if (null !== $appInfo) {
            $string = $appInfo['name'];
            if (null !== $appInfo['version']) {
                $string .= '/' . $appInfo['version'];
            }
            if (null !== $appInfo['url']) {
                $string .= ' (' . $appInfo['url'] . ')';
            }

            return $string;
        }

        return null;
    }

    /**
     * @static
     *
     * @param string $accessToken
     * @param null   $clientInfo
     *
     * @return array
     */
    private static function _defaultHeaders($accessToken, $clientInfo = null)
    {
        $uaString = 'Wildduck/v1 PhpBindings/' . Wildduck::VERSION;

        $langVersion = \PHP_VERSION;
        $uname_disabled = \in_array('php_uname', \explode(',', \ini_get('disable_functions')), true);
        $uname = $uname_disabled ? '(disabled)' : \php_uname();

        $appInfo = Wildduck::getAppInfo();
        $ua = [
            'bindings_version' => Wildduck::VERSION,
            'lang' => 'php',
            'lang_version' => $langVersion,
            'publisher' => 'zone-eu',
            'uname' => $uname,
        ];
        if ($clientInfo) {
            $ua = \array_merge($clientInfo, $ua);
        }
        if (null !== $appInfo) {
            $uaString .= ' ' . self::_formatAppInfo($appInfo);
            $ua['application'] = $appInfo;
        }

        return [
            'X-Wildduck-Client-User-Agent' => \json_encode($ua),
            'User-Agent' => $uaString,
            'X-Access-Token' => $accessToken,
        ];
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @param array $headers
     *
     * @throws Exception\AuthenticationException
     * @throws Exception\ApiConnectionException
     *
     * @return array
     */
    private function _requestRaw($method, $url, $params, $headers)
    {
        $myApiKey = $this->_accessToken;
        if (!$myApiKey) {
            $myApiKey = Wildduck::$accessToken;
        }

        // Clients can supply arbitrary additional keys to be included in the
        // X-Wildduck-Client-User-Agent header via the optional getUserAgentInfo()
        // method
        $clientUAInfo = null;
        if (\method_exists($this->httpClient(), 'getUserAgentInfo')) {
            $clientUAInfo = $this->httpClient()->getUserAgentInfo();
        }

        $absUrl = $this->_apiBase . $url;
//        $params = self::_encodeObjects($params);
        $defaultHeaders = $this->_defaultHeaders($myApiKey, $clientUAInfo);
        if (Wildduck::$apiVersion) {
            $defaultHeaders['Wildduck-Version'] = Wildduck::$apiVersion;
        }

        $hasFile = false;
        foreach ($params as $k => $v) {
            if (\is_resource($v)) {
                $hasFile = true;
                $params[$k] = self::_processResourceParam($v);
            } elseif ($v instanceof \CURLFile) {
                $hasFile = true;
            }
        }

        if ($hasFile) {
            $defaultHeaders['Content-Type'] = 'multipart/form-data';
        } else {
            $defaultHeaders['Content-Type'] = 'application/json';
        }

        $combinedHeaders = \array_merge($defaultHeaders, $headers);
        $rawHeaders = [];

        foreach ($combinedHeaders as $header => $value) {
            $rawHeaders[] = $header . ': ' . $value;
        }

        list($rbody, $rcode, $rheaders) = $this->httpClient()->request(
            $method,
            $absUrl,
//            $combinedHeaders,
            $rawHeaders,
            $params,
            $hasFile
        );

        return [$rbody, $rcode, $rheaders, $myApiKey];
    }

    /**
     * @param resource $resource
     *
     * @throws Exception\InvalidArgumentException
     *
     * @return \CURLFile|string
     */
    private function _processResourceParam($resource)
    {
        if ('stream' !== \get_resource_type($resource)) {
            throw new Exception\InvalidArgumentException(
                'Attempted to upload a resource that is not a stream'
            );
        }

        $metaData = \stream_get_meta_data($resource);
        if ('plainfile' !== $metaData['wrapper_type']) {
            throw new Exception\InvalidArgumentException(
                'Only plainfile resource streams are supported'
            );
        }

        // We don't have the filename or mimetype, but the API doesn't care
        return new \CURLFile($metaData['uri']);
    }

    /**
     * @param string $rbody
     * @param int    $rcode
     * @param array  $rheaders
     *
     * @throws Exception\UnexpectedValueException
     * @throws Exception\ApiErrorException
     *
     * @return array
     */
    private function _interpretResponse($rbody, $rcode, $rheaders)
    {
        $resp = \json_decode($rbody, true);
        $jsonError = \json_last_error();
        if (null === $resp && \JSON_ERROR_NONE !== $jsonError) {
            $msg = "Invalid response body from API: {$rbody} "
              . "(HTTP response code was {$rcode}, json_last_error() was {$jsonError})";

            throw new Exception\UnexpectedValueException($msg, $rcode);
        }

        if ($rcode < 200 || $rcode >= 300 || isset($resp['error']) || isset($resp['code'])) {
            $this->handleErrorResponse($rbody, $rcode, $rheaders, $resp);
        }

        return $resp;
    }

    /**
     * @static
     *
     * @param HttpClient\ClientInterface $client
     */
    public static function setHttpClient($client)
    {
        self::$_httpClient = $client;
    }

    /**
     * @return HttpClient\ClientInterface
     */
    private function httpClient()
    {
        if (!self::$_httpClient) {
            self::$_httpClient = HttpClient\CurlClient::instance();
        }

        return self::$_httpClient;
    }
}
