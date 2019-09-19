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
use Wildduck\Exceptions\UriNotFoundException;
use Wildduck\Http\Request;
use Wildduck\Util\Uri;

/**
 * Main wrapper for Wildduck API Client.
 *
 * @package Wildduck
 *
 * @method Addresses addresses()
 * @method ApplicationPasswords applicationPasswords()
 * @method Authentication authentication()
 * @method Autoreplies autoreplies()
 * @method Filters filters()
 * @method Mailboxes mailboxes()
 * @method Messages messages()
 * @method Submission submission()
 * @method TwoFactorAuth twoFactorAuth()
 * @method Users users()
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
     * Wildduck User authentication token
     * @var string $userToken
     */
    private $userToken = null;

    /**
     * Return raw GuzzleResponse instance instead of the parsed results
     * @var bool $raw
     */
    private $raw = false;

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
     * @return string
     */
    public function getUserToken()
    {
        return $this->userToken;
    }

    /**
     * @param string|null $userToken
     * @return Client
     */
    public function setUserToken(string $userToken = null): Client
    {
        if ($userToken === '') {
            $this->userToken = null;
            return $this;
        }

        $this->userToken = $userToken;
        return $this;
    }

    /**
     * @param string $keyword
     * @param array $data
     * @return string|null
     */
    public function resolveUri(string $keyword, array $data)
    {
        try {
            return Uri::get($keyword, $data);
        } catch (UriNotFoundException $e) {
            return null;
        }
    }

    /**
     * @return bool
     */
    public function isRaw()
    {
        return $this->raw;
    }

    /**
     * @param bool $raw
     * @return Client
     */
    public function setRaw(bool $raw): Client
    {
        $this->raw = $raw;
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
        if (strtolower($name) === 'raw') {
            return self::$client->setRaw(true);
        }

        if (strtolower($name) === 'token') {
            return self::$client->setUserToken($arguments[0]);
        }

        if (strtolower($name) === 'request') {
            return Request::class;
        }

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
        if (strtolower($name) === 'raw') {
            return self::$client->setRaw(true);
        }

        if (strtolower($name) === 'token') {
            return self::$client->setUserToken($arguments[0]);
        }

        if (strtolower($name) === 'request') {
            return new Request;
        }

        $class = "Wildduck\\Api\\" . ucfirst($name);
        if (!class_exists($class)) {
            throw new ApiClassNotFoundException("API class $class not found");
        }

        return new $class;
    }
}
