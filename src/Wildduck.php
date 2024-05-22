<?php

namespace Zone\Wildduck;

use Zone\Wildduck\Util\LoggerInterface;
use Zone\Wildduck\Util\DefaultLogger;

/**
 * Class Wildduck.
 */
class Wildduck
{
    /** @var string The Wildduck API key to be used for requests. */
    public static string $accessToken = '';

    /** @var string The base URL for the Wildduck API. */
    public static string $apiBase = 'https://localhost:8080';

    /** @var string|null Active session identifier */
    public static string|null $session = null;

    /** @var string|null Originating request IP */
    public static string|null $ip = null;

    /** @var null|string The version of the Wildduck API to use for requests. */
    public static string|null $apiVersion = null;

    /** @var string Path to the CA bundle used to verify SSL certificates */
    public static string $caBundlePath = '';

    /** @var bool Defaults to true. */
    public static bool $verifySslCerts = true;

    /** @var array|null The application's information (name, version, URL) */
    public static array|null $appInfo = null;

    /**
     * @var null|Util\LoggerInterface the logger to which the library will
     *   produce messages
     */
    public static null|LoggerInterface $logger = null;

    /** @var int Maximum number of request retries */
    public static int $maxNetworkRetries = 0;

    /** @var float Maximum delay between retries, in seconds */
    private static float $maxNetworkRetryDelay = 2.0;

    /** @var float Maximum delay between retries, in seconds, that will be respected from the Wildduck API */
    private static float $maxRetryAfter = 60.0;

    /** @var float Initial delay between retries, in seconds */
    private static float $initialNetworkRetryDelay = 0.5;

    public const string VERSION = '1.1.0';

    public static function getApiBase(): string
    {
        return self::$apiBase;
    }

    /**
     * @return string the access token used for requests
     */
    public static function getAccessToken(): string
    {
        return self::$accessToken;
    }

    /**
     * @return Util\LoggerInterface the logger to which the library will
     *   produce messages
     */
    public static function getLogger(): LoggerInterface
    {
        return self::$logger ?? new DefaultLogger();
    }

    /**
     * @param Util\LoggerInterface $logger the logger to which the library
     *   will produce messages
     */
    public static function setLogger(LoggerInterface $logger): void
    {
        self::$logger = $logger;
    }

    public static function setApiBase(string $apiBase): void
    {
        self::$apiBase = $apiBase;
    }

    /**
     * Sets the access token to be used for requests.
     */
    public static function setAccessToken(string $accessToken): void
    {
        self::$accessToken = $accessToken;
    }

    /**
     * Sets active session identifier
     */
    public static function setSession(string|null $session): void
    {
        self::$session = $session;
    }

    /**
     * Sets originating request IP
     */
    public static function setIp(string|null $ip): void
    {
        self::$ip = $ip;
    }

    /**
     * @return string The API version used for requests. null if we're using the
     *    latest version.
     */
    public static function getApiVersion(): string
    {
        return self::$apiVersion;
    }

    /**
     * @param string $apiVersion the API version to use for requests
     */
    public static function setApiVersion(string $apiVersion): void
    {
        self::$apiVersion = $apiVersion;
    }

    private static function getDefaultCABundlePath(): string
    {
        return dirname(__DIR__) . '/data/ca-certificates.crt';
    }

    public static function getCABundlePath(): string
    {
        return self::$caBundlePath ?: self::getDefaultCABundlePath();
    }

    public static function setCABundlePath(string $caBundlePath): void
    {
        self::$caBundlePath = $caBundlePath;
    }

    public static function getVerifySslCerts(): bool
    {
        return self::$verifySslCerts;
    }

    public static function setVerifySslCerts(bool $verify): void
    {
        self::$verifySslCerts = $verify;
    }

    /**
     * @return array|null The application's information
     */
    public static function getAppInfo(): array|null
    {
        return self::$appInfo;
    }

    /**
     * @param string $appName The application's name
     * @param null|string $appVersion The application's version
     * @param null|string $appUrl The application's URL
     * @param null|string $appPartnerId The application's partner ID
     */
    public static function setAppInfo(string $appName, null|string $appVersion = null, null|string $appUrl = null, null|string $appPartnerId = null): void
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
    public static function getMaxNetworkRetries(): int
    {
        return self::$maxNetworkRetries;
    }

    /**
     * @param int $maxNetworkRetries Maximum number of request retries
     */
    public static function setMaxNetworkRetries(int $maxNetworkRetries): void
    {
        self::$maxNetworkRetries = $maxNetworkRetries;
    }

    /**
     * @return float Maximum delay between retries, in seconds
     */
    public static function getMaxNetworkRetryDelay(): float
    {
        return self::$maxNetworkRetryDelay;
    }

    /**
     * @return float Maximum delay between retries, in seconds, that will be respected from the Wildduck API
     */
    public static function getMaxRetryAfter(): float
    {
        return self::$maxRetryAfter;
    }

    /**
     * @return float Initial delay between retries, in seconds
     */
    public static function getInitialNetworkRetryDelay(): float
    {
        return self::$initialNetworkRetryDelay;
    }
}
