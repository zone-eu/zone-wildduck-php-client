<?php

namespace Wildduck;

use Wildduck\Api\Address;
use Wildduck\Api\Authentication;
use Wildduck\Api\Users;
use Wildduck\Exceptions\ApiClassNotFoundException;

/**
 * Main wrapper for Wildduck API Client.
 *
 * @package Wildduck
 *
 * @method static Address address()
 * @method static Authentication authentication()
 * @method static Users users()
 */
class Client
{

    /**
     * Singleton instance.
     *
     * @var Client $client
     */
    private $client = null;

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
     * Client constructor. Build singleton if it doesn't exist.
     *
     * @return Client
     */
    public function __construct()
    {
        if ($this->client === null) {
            $this->client = $this;
        }

        return $this->client;
    }

    /**
     * Get singleton instance.
     *
     * @return Client
     */
    public static function instance()
    {
        return new self;
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
    public function setHost(string $host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * Get debug mode state.
     *
     * @return bool
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * Enable/Disable debug mode.
     *
     * @param bool $debug
     * @return $this
     */
    public function setDebug(bool $debug) {
        $this->debug = $debug;
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
