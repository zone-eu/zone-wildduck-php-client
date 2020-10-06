<?php

namespace Zone\Wildduck;

/**
 * Class Wildduck.
 */
class Wildduck
{
    /** @var string The Wildduck API key to be used for requests. */
    public static $accessToken;

    /** @var string The base URL for the Wildduck API. */
    public static $apiBase = 'https://localhost:8080';

    /** @var string|null Active session identifier */
    public static $session = null;

    /** @var string|null Originating request IP */
    public static $ip = null;

    /** @var null|string The version of the Wildduck API to use for requests. */
    public static $apiVersion = null;

    /** @var string Path to the CA bundle used to verify SSL certificates */
    public static $caBundlePath = null;

    /** @var bool Defaults to true. */
    public static $verifySslCerts = true;

    /** @var array The application's information (name, version, URL) */
    public static $appInfo = null;

    /**
     * @var null|Util\LoggerInterface the logger to which the library will
     *   produce messages
     */
    public static $logger = null;

    /** @var int Maximum number of request retries */
    public static $maxNetworkRetries = 0;

    /** @var float Maximum delay between retries, in seconds */
    private static $maxNetworkRetryDelay = 2.0;

    /** @var float Maximum delay between retries, in seconds, that will be respected from the Wildduck API */
    private static $maxRetryAfter = 60.0;

    /** @var float Initial delay between retries, in seconds */
    private static $initialNetworkRetryDelay = 0.5;

    const VERSION = '1.1.0';

    public static function getApiBase()
    {
        return self::$apiBase;
    }

    /**
     * @return string the access token used for requests
     */
    public static function getAccessToken()
    {
        return self::$accessToken;
    }

    /**
     * @return Util\LoggerInterface the logger to which the library will
     *   produce messages
     */
    public static function getLogger()
    {
        if (null === self::$logger) {
            return new Util\DefaultLogger();
        }

        return self::$logger;
    }

    /**
     * @param Util\LoggerInterface $logger the logger to which the library
     *   will produce messages
     */
    public static function setLogger($logger)
    {
        self::$logger = $logger;
    }

    public static function setApiBase($apiBase)
    {
        self::$apiBase = $apiBase;
    }

    /**
     * Sets the access token to be used for requests.
     *
     * @param string $accessToken
     */
    public static function setAccessToken($accessToken)
    {
        self::$accessToken = $accessToken;
    }

    /**
     * Sets active session identifier
     *
     * @param string|null $session
     */
    public static function setSession($session)
    {
        self::$session = $session;
    }

    /**
     * Sets originating request IP
     *
     * @param string|null $ip
     */
    public static function setIp($ip)
    {
        self::$ip = $ip;
    }

    /**
     * @return string The API version used for requests. null if we're using the
     *    latest version.
     */
    public static function getApiVersion()
    {
        return self::$apiVersion;
    }

    /**
     * @param string $apiVersion the API version to use for requests
     */
    public static function setApiVersion($apiVersion)
    {
        self::$apiVersion = $apiVersion;
    }

    /**
     * @return string
     */
    private static function getDefaultCABundlePath()
    {
        return \realpath(__DIR__ . '/../data/ca-certificates.crt');
    }

    /**
     * @return string
     */
    public static function getCABundlePath()
    {
        return self::$caBundlePath ?: self::getDefaultCABundlePath();
    }

    /**
     * @param string $caBundlePath
     */
    public static function setCABundlePath($caBundlePath)
    {
        self::$caBundlePath = $caBundlePath;
    }

    /**
     * @return bool
     */
    public static function getVerifySslCerts()
    {
        return self::$verifySslCerts;
    }

    /**
     * @param bool $verify
     */
    public static function setVerifySslCerts($verify)
    {
        self::$verifySslCerts = $verify;
    }

    /**
     * @return array | null The application's information
     */
    public static function getAppInfo()
    {
        return self::$appInfo;
    }

    /**
     * @param string $appName The application's name
     * @param null|string $appVersion The application's version
     * @param null|string $appUrl The application's URL
     * @param null|string $appPartnerId The application's partner ID
     */
    public static function setAppInfo($appName, $appVersion = null, $appUrl = null, $appPartnerId = null)
    {
        self::$appInfo = self::$appInfo ?: [];
        self::$appInfo['name'] = $appName;
        self::$appInfo['partner_id'] = $appPartnerId;
        self::$appInfo['url'] = $appUrl;
        self::$appInfo['version'] = $appVersion;
    }

    /**
     * @return int Maximum number of request retries
     */
    public static function getMaxNetworkRetries()
    {
        return self::$maxNetworkRetries;
    }

    /**
     * @param int $maxNetworkRetries Maximum number of request retries
     */
    public static function setMaxNetworkRetries($maxNetworkRetries)
    {
        self::$maxNetworkRetries = $maxNetworkRetries;
    }

    /**
     * @return float Maximum delay between retries, in seconds
     */
    public static function getMaxNetworkRetryDelay()
    {
        return self::$maxNetworkRetryDelay;
    }

    /**
     * @return float Maximum delay between retries, in seconds, that will be respected from the Wildduck API
     */
    public static function getMaxRetryAfter()
    {
        return self::$maxRetryAfter;
    }

    /**
     * @return float Initial delay between retries, in seconds
     */
    public static function getInitialNetworkRetryDelay()
    {
        return self::$initialNetworkRetryDelay;
    }
}
