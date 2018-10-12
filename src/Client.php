<?php

namespace Wildduck;

use Wildduck\Api\Addresses;
use Wildduck\Api\ApplicationPasswords;
use Wildduck\Api\Authentication;
use Wildduck\Api\Autoreplies;
use Wildduck\Api\Filters;
use Wildduck\Api\Mailboxes;
use Wildduck\Api\Messages;
use Wildduck\Api\Submission;
use Wildduck\Api\TwoFactorAuth;
use Wildduck\Api\Users;
use Wildduck\Exceptions\ApiClassNotFoundException;

/**
 * Main wrapper for Wildduck API Client.
 *
 * @package Wildduck
 *
 * @method static Addresses addresses()
 * @method static ApplicationPasswords applicationPasswords()
 * @method static Authentication authentication()
 * @method static Autoreplies autoreplies()
 * @method static Filters filters()
 * @method static Mailboxes mailboxes()
 * @method static Messages messages()
 * @method static Submission submission()
 * @method static TwoFactorAuth twoFactorAuth()
 * @method static Users users()
 */
class Client
{

    /**
     * Singleton instance.
     *
     * @var Client $client
     */
    private static $client = null;

    /**
     * Wildduck Server API endpoint
     * @var string $host
     */
    private $host = '';

    /**
     * See debug output of some errors.
     * @var bool $debug
     */
    private $debug = false;

    /**
     * Wildduck API access token if authentication is enabled
     * @var string $accessToken
     */
    private $accessToken = null;

    /**
     * Get or create a singleton instance.
     *
     * @return Client
     */
    public static function instance() : Client
    {
        if (self::$client === null) {
            self::$client = new self;
        }

        return self::$client;
    }

    /**
     * Get Wildduck Server API host.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set Wildduck Server API host.
     *
     * @param string $host
     * @return $this
     */
    public function setHost(string $host) : Client
    {
        $this->host = $host;
        return $this;
    }

    /**
     * Get debug mode state.
     *
     * @return bool
     */
    public function getDebug() : bool
    {
        return $this->debug;
    }

    /**
     * Enable/Disable debug mode.
     *
     * @param bool $debug
     * @return $this
     */
    public function setDebug(bool $debug) : Client
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     * @return Client
     */
    public function setAccessToken(string $accessToken = null): Client
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws ApiClassNotFoundException
     */
    public static function __callStatic(string $name, array $arguments = [])
    {
        $class = "Wildduck\\Api\\" . ucfirst($name);
        if (!class_exists($class)) {
            throw new ApiClassNotFoundException("API class $class not found");
        }

        return new $class;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws ApiClassNotFoundException
     */
    public function __call(string $name, array $arguments = [])
    {
        $class = "Wildduck\\Api\\" . ucfirst($name);
        if (!class_exists($class)) {
            throw new ApiClassNotFoundException("API class $class not found");
        }

        return new $class;
    }
}
