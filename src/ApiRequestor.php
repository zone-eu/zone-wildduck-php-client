<?php

namespace Zone\Wildduck;

use CURLFile;
use Exception;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidArgumentException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\UnexpectedValueException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\HttpClient\ClientInterface;
use Zone\Wildduck\HttpClient\CurlClient;
use Zone\Wildduck\Util\Util;

use function array_merge;
use function explode;
use function get_resource_type;
use function in_array;
use function ini_get;
use function is_array;
use function is_resource;
use function json_decode;
use function json_encode;
use function json_last_error;
use function method_exists;
use function php_uname;
use function stream_get_meta_data;

use const JSON_ERROR_NONE;
use const PHP_VERSION;

/**
 * Class ApiRequestor.
 */
class ApiRequestor
{
    public const string CODE_INPUT_VALIDATION_ERROR = 'InputValidationError';

    public const string CODE_INVALID_TOKEN = 'InvalidToken';

    public const string CODE_AUTH_FAILED = 'AuthFailed';

    public const string CODE_INTERNAL_SERVER = 'InternalServer';

    public const string CODE_INVALID_DATABASE = 'InternalDatabaseError';
    private static ?ClientInterface $_httpClient = null;
    private readonly string $_apiBase;
    private readonly string|null $_accessToken;

    /**
     * ApiRequestor constructor.
     *
     * @param null|string $accessToken
     * @param null|string $apiBase
     */
    public function __construct(?string $accessToken = null, ?string $apiBase = null)
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
     * @param HttpClient\ClientInterface $client
     */
    public static function setHttpClient(ClientInterface $client): void
    {
        self::$_httpClient = $client;
    }

    /**
     * @static
     *
     * @param array|bool|mixed $d
     *
     * @return array|mixed|string
     */
    protected static function _encodeObjects(mixed $d): mixed
    {
        if (is_array($d)) {
            $res = [];
            foreach ($d as $k => $v) {
                $res[$k] = self::_encodeObjects($v);
            }

            return $res;
        }

        return Util::utf8($d);
    }

    /**
     * @param string $method The HTTP method being used
     * @param string $url The URL being requested, including domain and protocol
     * @param mixed $params Must be KV pairs when not uploading files otherwise anything is allowed, string is expected for file upload. Can be nested for arrays and hashes
     * @param array|null $headers Headers to be used in the request (full strings, not KV pairs)
     * @param bool $raw Whether to return the raw response or JSON decode it
     * @param bool $fileUpload Whether the request is a file upload
     *
     * @return array{0: ApiResponse, 1: null|string} an array whose first element is the response object and second is the api key
     *
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws InvalidDatabaseException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function request(string $method, string $url, mixed $params = null, array|null $headers = null, bool $raw = false, bool $fileUpload = false): array
    {
        $params = $params ?: [];
        $headers = $headers ?: [];

        [$rBody, $rCode, $rHeaders, $myApiKey] = $this->_requestRaw($method, $url, $params, $headers, $fileUpload);
        $json = null;
        if ($rCode < 200 || $rCode >= 300) {
            $resp = json_decode((string)$rBody, true);
            $jsonError = json_last_error();
            if ($resp && $jsonError === JSON_ERROR_NONE) {
                $this->handleErrorResponse($rBody, $rCode, $resp);
            }
        }

        if (!$raw) {
            $json = $this->_interpretResponse($rBody, $rCode);
        }

        $resp = new ApiResponse($rBody, $rCode, $rHeaders, $json);

        return [$resp, $myApiKey];
    }

    /**
     * @param string $method The HTTP method being used
     * @param string $url The URL being requested, including domain and protocol
     * @param mixed $params Must be KV pairs when not uploading files otherwise anything is allowed, string is expected for file upload. Can be nested for arrays and hashes
     * @param array|null $headers Headers to be used in the request (full strings, not KV pairs)
     * @param bool $fileUpload
     * @return array{0: mixed, 1: int, 2: array, 3: null|string} an array whose first element is raw request body, second
     *    element is HTTP status code and third array of HTTP headers and fourth is api key
     *
     * @throws ApiConnectionException
     */
    private function _requestRaw(string $method, string $url, mixed $params, array|null $headers, bool $fileUpload): array
    {
        $myApiKey = $this->_accessToken;
        if (!$myApiKey) {
            $myApiKey = Wildduck::$accessToken;
        }

        // Clients can supply arbitrary additional keys to be included in the
        // X-Wildduck-Client-User-Agent header
        $clientUAInfo = null;

        $absUrl = $this->_apiBase . $url;
        $defaultHeaders = $this->_defaultHeaders($myApiKey, $clientUAInfo);
        if (Wildduck::$apiVersion) {
            $defaultHeaders['Wildduck-Version'] = Wildduck::$apiVersion;
        }

        $hasFile = false;
        if (!$fileUpload) {
            foreach ($params as $k => $v) {
                if (is_resource($v)) {
                    $hasFile = true;
                    /** @phpstan-ignore-next-line */
                    $params[$k] = $this->_processResourceParam($v);
                } elseif ($v instanceof CURLFile) {
                    $hasFile = true;
                }
            }
        }

        if ($fileUpload) {
            $defaultHeaders['Content-Type'] = 'application/binary';
        } elseif ($hasFile) {
            $defaultHeaders['Content-Type'] = 'multipart/form-data';
        } else {
            $defaultHeaders['Content-Type'] = 'application/json';
        }

        $combinedHeaders = array_merge($defaultHeaders, $headers);
        $rawHeaders = [];
        foreach ($combinedHeaders as $header => $value) {
            $rawHeaders[] = $header . ': ' . $value;
        }

        [$rBody, $rCode, $rHeaders] = $this->httpClient()->request(
            $method,
            $absUrl,
            $rawHeaders,
            $params,
            $hasFile || $fileUpload,
        );

        if (getenv('WDPC_REQUEST_LOGGING') === "true") {
            $this->logRequest(
                $method,
                $absUrl,
                $rawHeaders,
                $params,
                $hasFile,
                $fileUpload,
                [
                    'body' => $rBody,
                    'code' => $rCode,
                    'headers' => $rHeaders
                ]
            );
        }

        return [$rBody, $rCode, $rHeaders, $myApiKey];
    }

    private function httpClient(): ClientInterface
    {
        if (!self::$_httpClient instanceof ClientInterface) {
            self::$_httpClient = CurlClient::instance();
        }

        return self::$_httpClient;
    }

    /**
     * @static
     */
    private function _defaultHeaders(string $accessToken, array|null $clientInfo = null): array
    {
        $uaString = 'Wildduck/v1 PhpBindings/' . Wildduck::VERSION;

        $langVersion = PHP_VERSION;
        $uname_disabled = in_array('php_uname', explode(',', ini_get('disable_functions')), true);
        $uname = $uname_disabled ? '(disabled)' : php_uname();

        $appInfo = Wildduck::getAppInfo();
        $ua = [
            'bindings_version' => Wildduck::VERSION,
            'lang' => 'php',
            'lang_version' => $langVersion,
            'publisher' => 'zone-eu',
            'uname' => $uname,
        ];
        if ($clientInfo) {
            $ua = array_merge($clientInfo, $ua);
        }

        if (null !== $appInfo) {
            $uaString .= ' ' . $this->_formatAppInfo($appInfo);
            $ua['application'] = $appInfo;
        }

        return [
            'X-Wildduck-Client-User-Agent' => json_encode($ua),
            'User-Agent' => $uaString,
            'X-Access-Token' => $accessToken,
        ];
    }

    /**
     * @static
     * @param array|null $appInfo
     *
     * @return null|string
     */
    private function _formatAppInfo(array|null $appInfo): null|string
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
     * @param object $resource
     *
     * @return CURLFile
     * @throws InvalidArgumentException
     *
     */
    private function _processResourceParam(mixed $resource): CURLFile
    {
        /** @phpstan-ignore-next-line */
        if ('stream' !== get_resource_type($resource)) {
            throw new InvalidArgumentException(
                'Attempted to upload a resource that is not a stream'
            );
        }

        /** @phpstan-ignore-next-line */
        $metaData = stream_get_meta_data($resource);
        if ('plainfile' !== $metaData['wrapper_type']) {
            throw new InvalidArgumentException(
                'Only plainfile resource streams are supported'
            );
        }

        // We don't have the filename or mimetype, but the API doesn't care
        return new CURLFile($metaData['uri']);
    }

    private function logRequest(
        string $method,
        string $absUrl,
        array $headers,
        array $params,
        bool $hasFile,
        bool $fileUpload,
        array $response
    ): void {
        try {
            if (!preg_match(getenv("WDPC_REQUEST_LOGGING_PATTERN"), $absUrl)) {
                return;
            } // Only log things that the regex catches


            /**
             * Set up the directory.
             *  Create if it doesn't exist
             */
            $directory = rtrim(getenv("WDPC_REQUEST_LOGGING_DIRECTORY"), "/");
            if ($directory === '' || $directory === '0') {
                $message = "Wildduck php client tried to log a request, but no directory was set.";
                Wildduck::getLogger()->error($message);
                return;
            }

            $subDirectory = date('Y-m-d-H');
            $fullDirectory = sprintf('%s/%s/', $directory, $subDirectory);
            if (!is_dir($fullDirectory)) {
                $permissions = getenv('WDPC_REQUEST_LOGGING_FOLDER_PERMISSIONS');
                $permissions = $permissions ?: 0755;
                $createdDirectory = mkdir($fullDirectory, $permissions, true);

                if (!$createdDirectory) {
                    $message = sprintf('Wildduck php client tried to create a directory, but was unable to. Directory path: \'%s\'', $fullDirectory);
                    Wildduck::getLogger()->error($message);
                    return;
                }
            }


            /**
             * Create file.
             */
            $userId = 'noUserIdRequest';
            preg_match("/\/users\/([a-z0-9]*)/", $absUrl, $matches);
            if (isset($matches[1]) && $matches[1]) {
                $userId = $matches[1];
            }

            $randString = uniqid('', true);
            $filename = sprintf('%s-%s-%s.json', $method, $userId, $randString);

            if (!$handle = fopen($fullDirectory . $filename, 'wb')) {
                $message = sprintf('Wildduck php client cannot open file (%s%s)', $fullDirectory, $filename);
                Wildduck::getLogger()->error($message);
                return;
            }

            // Set up the data to be saved
            $data = [
                'method' => $method,
                'absUrl' => $absUrl,
                'hasFile' => $hasFile,
                'isFileUpload' => $fileUpload,
                'headers' => $headers,
                'response' => $response,
            ];

            if (json_encode($params)) {
                $data['params'] = $params;
            }

            // Write data to our opened file.
            if (fwrite($handle, json_encode($data)) === false) {
                $message = sprintf('Wildduck php client cannot write data to file: %s%s', $fullDirectory, $filename);
                Wildduck::getLogger()->error($message);
                fclose($handle);
                return;
            }

            fclose($handle);

            return;
        } catch (Exception $exception) {
            echo $exception->getMessage();
            Wildduck::getLogger()->error($exception->getMessage());
            return;
        }
    }

    /**
     * @param string $rbody a JSON string
     * @param int $rcode
     * @param array $resp
     *
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws InvalidDatabaseException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function handleErrorResponse(string $rbody, int $rcode, array $resp): void
    {
        if (!isset($resp['error']) && !isset($resp['code'])) {
            $msg = sprintf('Invalid response object from API: %s ', $rbody)
                . sprintf('(HTTP response code was %d)', $rcode);

            throw new UnexpectedValueException($msg);
        }

        if (isset($resp['code'])) {
            $this->_specificAPIError($resp['code'], $resp['message'] ?? $resp['error'] ?? 'unknown error', $rcode);
        }

        throw new RequestFailedException($resp['error']);
    }

    /**
     * @static
     *
     * @param string $code
     * @param string $error
     * @param int $rCode - The wildduck http response code
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ValidationException
     * @throws RequestFailedException
     * @throws InvalidDatabaseException
     */
    private function _specificAPIError(string $code, string $error, int $rCode = 0): void
    {
        switch ($code) {
            case static::CODE_INVALID_TOKEN:
                throw new InvalidAccessTokenException('Access token used for request was invalid');
            case static::CODE_AUTH_FAILED:
                throw new AuthenticationFailedException($error);
            case static::CODE_INPUT_VALIDATION_ERROR:
                throw new ValidationException($error);
            case static::CODE_INVALID_DATABASE:
                throw new InvalidDatabaseException($error);
        }

        throw new RequestFailedException($error, $code, $rCode);
    }

    /**
     * @param string $rbody
     * @param int $rcode
     *
     * @return array
     *
     * @throws UnexpectedValueException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws InvalidDatabaseException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    private function _interpretResponse(string $rbody, int $rcode): array
    {
        $resp = json_decode($rbody, true);
        $jsonError = json_last_error();

        if (null === $resp && JSON_ERROR_NONE !== $jsonError) {
            $msg = sprintf('Invalid response body from API: %s ', $rbody)
                . sprintf('(HTTP response code was %d, json_last_error() was %s)', $rcode, $jsonError);

            throw new UnexpectedValueException($msg, $rcode);
        }

        if ($rcode < 200 || $rcode >= 300 || isset($resp['error']) || (isset($resp['code']) && $resp['code'] !== 'TaskScheduled')) {
            $this->handleErrorResponse($rbody, $rcode, $resp);
        }

        return $resp;
    }
}
