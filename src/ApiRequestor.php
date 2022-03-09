<?php

namespace Zone\Wildduck;

use CURLFile;
use Exception;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\InvalidArgumentException;
use Zone\Wildduck\Exception\UnexpectedValueException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

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

    const CODE_INPUT_VALIDATION_ERROR = 'InputValidationError';
    const CODE_INVALID_TOKEN = 'InvalidToken';
    const CODE_AUTH_FAILED = 'AuthFailed';
    const CODE_INTERNAL_SERVER = 'InternalServer';
    const CODE_INVALID_DATABASE = 'InternalDatabaseError';

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

        if (is_array($d)) {
            $res = [];
            foreach ($d as $k => $v) {
                $res[$k] = self::_encodeObjects($v);
            }

            return $res;
        }

        return Util\Util::utf8($d);
    }

    /**
     * @param string $method
     * @param string $url
     * @param null|array $params
     * @param null|array $headers
     * @param bool $raw
     * @param bool $fileUpload
     *
     * @return array tuple containing (ApiResponse, API key)
     *
     * @throws ApiConnectionException
     * @throws UnexpectedValueException
     * @throws AuthenticationFailedException
     * @throws RequestFailedException
     * @throws ValidationException
     * @throws InvalidAccessTokenException
     * @throws InvalidDatabaseException
     *
     */
    public function request($method, $url, $params = null, $headers = null, $raw = false, $fileUpload = false): array
    {
        $params = $params ?: [];
        $headers = $headers ?: [];

        list(
            $rBody, $rCode, $rHeaders, $myApiKey
        ) = $this->_requestRaw($method, $url, $params, $headers, $fileUpload);

        $json = null;

        if ($rCode < 200 || $rCode >= 300) {
            $resp = json_decode($rBody, true);
            $jsonError = json_last_error();
            if ($resp && $jsonError === JSON_ERROR_NONE) {
                $this->handleErrorResponse($rBody, $rCode, $rHeaders, $resp);
            }
        }

        if (!$raw) {
            $json = $this->_interpretResponse($rBody, $rCode, $rHeaders);
        }

        $resp = new ApiResponse($rBody, $rCode, $rHeaders, $json);

        return [$resp, $myApiKey];
    }

    /**
     * @param string $rbody a JSON string
     * @param int $rcode
     * @param array $rheaders
     * @param array $resp
     *
     * @throws UnexpectedValueException
     * @throws AuthenticationFailedException
     * @throws RequestFailedException
     * @throws ValidationException
     * @throws InvalidAccessTokenException
     * @throws InvalidDatabaseException
     */
    public function handleErrorResponse($rbody, $rcode, $rheaders, $resp)
    {
        if (!is_array($resp) || (!isset($resp['error']) && !isset($resp['code']))) {
            $msg = "Invalid response object from API: {$rbody} "
                . "(HTTP response code was {$rcode})";

            throw new UnexpectedValueException($msg);
        }

        if (isset($resp['code'])) {
            self::_specificAPIError($resp['code'], $resp['message'] ?? $resp['error'] ?? 'unknown error', $rcode);
        }

        throw new RequestFailedException($resp['error']);
    }

    /**
     * @static
     *
     * @param $code
     * @param $error
     * @param int $rCode - The wildduck http response code
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ValidationException
     * @throws RequestFailedException
     * @throws InvalidDatabaseException
     */
    private static function _specificAPIError($code, $error, int $rCode = 0)
    {
        switch ($code) {
            case static::CODE_INVALID_TOKEN:
                throw new InvalidAccessTokenException('Access token used for request was invalid');
            case static::CODE_AUTH_FAILED:
                throw new AuthenticationFailedException($error);
            case static::CODE_INPUT_VALIDATION_ERROR:
                throw new ValidationException($error);
            case static::CODE_INVALID_DATABASE;
                throw new InvalidDatabaseException($error);
        }

        throw new RequestFailedException($error, $code, $rCode);
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
     * @param null $clientInfo
     *
     * @return array
     */
    private static function _defaultHeaders($accessToken, $clientInfo = null)
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
            $uaString .= ' ' . self::_formatAppInfo($appInfo);
            $ua['application'] = $appInfo;
        }

        return [
            'X-Wildduck-Client-User-Agent' => json_encode($ua),
            'User-Agent' => $uaString,
            'X-Access-Token' => $accessToken,
        ];
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @param array $headers
     * @param $fileUpload
     *
     * @return array
     *
     * @throws UnexpectedValueException
     * @throws ApiConnectionException
     */
    private function _requestRaw($method, $url, $params, $headers, $fileUpload): array
    {
        $myApiKey = $this->_accessToken;
        if (!$myApiKey) {
            $myApiKey = Wildduck::$accessToken;
        }

        // Clients can supply arbitrary additional keys to be included in the
        // X-Wildduck-Client-User-Agent header via the optional getUserAgentInfo()
        // method
        $clientUAInfo = null;
        if (method_exists($this->httpClient(), 'getUserAgentInfo')) {
            $clientUAInfo = $this->httpClient()->getUserAgentInfo();
        }

        $absUrl = $this->_apiBase . $url;
//        $params = self::_encodeObjects($params);
        $defaultHeaders = $this->_defaultHeaders($myApiKey, $clientUAInfo);
        if (Wildduck::$apiVersion) {
            $defaultHeaders['Wildduck-Version'] = Wildduck::$apiVersion;
        }

        $hasFile = false;
        if (!$fileUpload) {
            foreach ($params as $k => $v) {
                if (is_resource($v)) {
                    $hasFile = true;
                    $params[$k] = self::_processResourceParam($v);
                } elseif ($v instanceof CURLFile) {
                    $hasFile = true;
                }
            }
        }

        if ($fileUpload) {
            $defaultHeaders['Content-Type'] = 'application/binary';
        } else {
            if ($hasFile) {
                $defaultHeaders['Content-Type'] = 'multipart/form-data';
            } else {
                $defaultHeaders['Content-Type'] = 'application/json';
            }
        }

        $combinedHeaders = array_merge($defaultHeaders, $headers);
        $rawHeaders = [];

        foreach ($combinedHeaders as $header => $value) {
            $rawHeaders[] = $header . ': ' . $value;
        }

        list($rBody, $rCode, $rHeaders) = $this->httpClient()->request(
            $method,
            $absUrl,
//            $combinedHeaders,
            $rawHeaders,
            $params,
            $hasFile,
            $fileUpload,
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

    private function logRequest(
        string $method,
        string $absUrl,
        array $headers,
        $params,
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
            if (!$directory) {
                error_log("Wildduck php client tried to log a request, but no directory was set.");
                return;
            }

            $subDirectory = date('Y-m-d-H');
            $fullDirectory = "$directory/$subDirectory/";
            if (!is_dir($fullDirectory)) {
                $permissions = getenv('WDPC_REQUEST_LOGGING_FOLDER_PERMISSIONS');
                $permissions = !$permissions ? 0755 : $permissions;
                $createdDirectory = mkdir($fullDirectory, $permissions, true);

                if (!$createdDirectory) {
                    error_log(
                        "Wildduck php client tried to create a directory, but was unable to. Directory path: '$fullDirectory'"
                    );
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

            $randString = uniqid();
            $filename = "$method-$userId-$randString.json";

            if (!$handle = fopen($fullDirectory . $filename, 'w')) {
                error_log("Wildduck php client cannot open file ($fullDirectory$filename)");
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
                error_log("Wildduck php client cannot write data to file: $fullDirectory$filename");
                fclose($handle);
                return;
            }
            fclose($handle);

            return;
        } catch (Exception $e) {
            echo $e->getMessage();
            error_log($e->getMessage());
            return;
        }
    }

    /**
     * @param resource $resource
     *
     * @return CURLFile|string
     * @throws InvalidArgumentException
     *
     */
    private function _processResourceParam($resource)
    {
        if ('stream' !== get_resource_type($resource)) {
            throw new InvalidArgumentException(
                'Attempted to upload a resource that is not a stream'
            );
        }

        $metaData = stream_get_meta_data($resource);
        if ('plainfile' !== $metaData['wrapper_type']) {
            throw new InvalidArgumentException(
                'Only plainfile resource streams are supported'
            );
        }

        // We don't have the filename or mimetype, but the API doesn't care
        return new CURLFile($metaData['uri']);
    }

    /**
     * @param string $rbody
     * @param int $rcode
     * @param array $rheaders
     *
     * @return array
     *
     * @throws UnexpectedValueException
     * @throws AuthenticationFailedException
     * @throws RequestFailedException
     * @throws ValidationException
     * @throws InvalidAccessTokenException
     * @throws InvalidDatabaseException
     */
    private function _interpretResponse($rbody, $rcode, $rheaders): array
    {
        $resp = json_decode($rbody, true);
        $jsonError = json_last_error();
        if (null === $resp && JSON_ERROR_NONE !== $jsonError) {
            $msg = "Invalid response body from API: {$rbody} "
                . "(HTTP response code was {$rcode}, json_last_error() was {$jsonError})";

            throw new UnexpectedValueException($msg, $rcode);
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
