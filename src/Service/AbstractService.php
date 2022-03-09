<?php

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Collection2;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidArgumentException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\UnexpectedValueException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\WildduckClientInterface;

/**
 * Abstract base class for all services.
 */
abstract class AbstractService
{
    /**
     * @var WildduckClientInterface
     */
    protected $client;

    /**
     * Initializes a new instance of the {@link AbstractService} class.
     *
     * @param WildduckClientInterface $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * Gets the client used by this service to send requests.
     *
     * @return WildduckClientInterface
     */
    public function getClient(): WildduckClientInterface
    {
        return $this->client;
    }

    /**
     * Translate null values to empty strings. For service methods,
     * we interpret null as a request to unset the field, which
     * corresponds to sending an empty string for the field to the
     * API.
     *
     * @param null|array $params
     */
    private static function formatParams($params)
    {
        if (null === $params) {
            return null;
        }

        if (is_resource($params)) return $params;

        \array_walk_recursive($params, function (&$value, $key) {
            if (null === $value) {
                $value = '';
            }
        });

        return $params;
    }

    /**
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    protected function file($method, $path, $params, $opts)
    {

        return $this->getClient()->request($method, $path, $params, $opts, true);
    }

    /**
     * @throws RequestFailedException
     * @throws InvalidAccessTokenException
     * @throws AuthenticationFailedException
     * @throws ApiConnectionException
     * @throws ValidationException
     */
    protected function request($method, $path, $params, $opts, $fileUpload = false)
    {
        if (null !== $object = $this->getObjectName()) {
            $opts['object'] = $object;
        }
        return $this->getClient()->request($method, $path, static::formatParams($params), $opts, $fileUpload);
    }

    /**
     * @throws ApiConnectionException
     * @throws UnexpectedValueException
     * @throws AuthenticationFailedException
     * @throws RequestFailedException
     * @throws ValidationException
     * @throws InvalidAccessTokenException
     */
    protected function requestCollection($method, $path, $params, $opts): Collection2
    {
        $opts['object'] = $this->getObjectName();
        return $this->getClient()->requestCollection($method, $path, static::formatParams($params), $opts);
    }

    protected function stream(string $method, string $path, $params, $opts): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return $this->getClient()->stream($method, $path, static::formatParams($params), $opts);
    }

    /**
     * @param string $basePath The string for sprintf
     * @param mixed $ids params to be replaced
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected function buildPath($basePath, ...$ids): string
    {
        foreach ($ids as $id) {
            if (null === $id || '' === \trim($id)) {
                $msg = 'The resource ID cannot be null or whitespace.';

                throw new InvalidArgumentException($msg);
            }
        }

        return \sprintf($basePath, ...\array_map('\urlencode', $ids));
    }

    protected function getObjectName()
    {
        $parts = explode('\\', get_called_class());
        $service = $parts[count($parts) - 1];
        $entityClass = implode('\\', [$parts[0], $parts[1]]) . '\\' . explode('Service', $service)[0];
        if (class_exists($entityClass)) {
            return $entityClass::OBJECT_NAME;
        }

        return null;
    }
}
