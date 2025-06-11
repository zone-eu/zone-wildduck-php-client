<?php

namespace Zone\Wildduck\Service;

use ErrorException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidArgumentException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\Resource\File;
use Zone\Wildduck\Util\RequestOptions;
use Zone\Wildduck\WildduckClientInterface;

/**
 * Abstract base class for all services.
 */
abstract class AbstractService
{
    /**
     * @var WildduckClientInterface
     */
    protected WildduckClientInterface $client;

    /**
     * Initializes a new instance of the {@link AbstractService} class.
     *
     * @param WildduckClientInterface $client
     */
    public function __construct(WildduckClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Upload file
     *
     * @param string $method
     * @param string $path
     * @param string $fileContent
     * @param array|null $opts
     *
     * @return mixed
     *
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function uploadFile(string $method, string $path, string $fileContent, array|null $opts): mixed
    {
        return $this->getClient()->request($method, $path, $fileContent, $opts, true);
    }

    /**
     * @param string $method
     * @param string $path
     * @param array|null $params Must be KV pairs
     * @param array|null $opts
     * @param bool $fileUpload
     *
     * @return mixed
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function request(string $method, string $path, array|null $params, array|null $opts, bool $fileUpload = false): mixed
    {
        if (null !== $this->getObjectName()) {
            $opts['object'] = $this->getObjectName();
        }

        return $this->getClient()->request($method, $path, $this->formatParams($params), $opts, $fileUpload);
    }

    /**
     * @return mixed
     */
    protected function getObjectName(): mixed
    {
        $parts = explode('\\', static::class);
        $service = $parts[count($parts) - 1];
        $entityClass = implode('\\', [$parts[0], $parts[1]]) . '\\' . explode('Service', $service)[0];
        if (class_exists($entityClass)) {
            return $entityClass::OBJECT_NAME;
        }

        return null;
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
     * @param array|null $params
     * @return array|null
     */
    private function formatParams(array|null $params): array|null
    {
        if (null === $params) {
            return null;
        }

        array_walk_recursive($params, static function (&$value): void {
            if (null === $value) {
                $value = '';
            }
        });

        return $params;
    }

    /**
     * @param string $method
     * @param string $path
     * @param array|null $params
     * @param array|RequestOptions|null $opts
     *
     * @return mixed
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws InvalidDatabaseException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function requestCollection(string $method, string $path, array|null $params, array|RequestOptions|null $opts): mixed
    {
        $opts['object'] = $this->getObjectName();
        return $this->getClient()->requestCollection($method, $path, $this->formatParams($params), $opts);
    }

    /**
     * @param string $method
     * @param string $path
     * @param array|null $params
     * @param array|null $opts
     *
     * @return StreamedResponse
     * @throws ErrorException
     */
    public function stream(string $method, string $path, array|null $params, array|null $opts): StreamedResponse
    {
        return $this->getClient()->stream($method, $path, $this->formatParams($params), $opts);
    }

    /**
     * @param string $basePath The string for sprintf
     * @param mixed $ids params to be replaced
     *
     * @throws InvalidArgumentException
     */
    public function buildPath(string $basePath, mixed ...$ids): string
    {
        foreach ($ids as $id) {
            if (null === $id || '' === trim((string) $id)) {
                $msg = 'The resource ID cannot be null or whitespace.';

                throw new InvalidArgumentException($msg);
            }
        }

        return sprintf($basePath, ...array_map('\urlencode', $ids));
    }
}
