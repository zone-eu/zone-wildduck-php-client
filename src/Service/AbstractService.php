<?php

namespace Zone\Wildduck\Service;

/**
 * Abstract base class for all services.
 */
abstract class AbstractService
{
    /**
     * @var \Zone\Wildduck\WildduckClientInterface
     */
    protected $client;

    /**
     * Initializes a new instance of the {@link AbstractService} class.
     *
     * @param \Zone\Wildduck\WildduckClientInterface $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * Gets the client used by this service to send requests.
     *
     * @return \Zone\Wildduck\WildduckClientInterface
     */
    public function getClient()
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
        \array_walk_recursive($params, function (&$value, $key) {
            if (null === $value) {
                $value = '';
            }
        });

        return $params;
    }

    protected function request($method, $path, $params, $opts)
    {
        if (null !== $object = $this->getObjectName()) {
            $opts['object'] = $object;
        }
        return $this->getClient()->request($method, $path, static::formatParams($params), $opts);
    }

    protected function requestCollection($method, $path, $params, $opts)
    {
        $opts['object'] = $this->getObjectName();
        return $this->getClient()->requestCollection($method, $path, static::formatParams($params), $opts);
    }

    protected function stream(string $method, string $path, $params, $opts)
    {
        return $this->getClient()->stream($method, $path, static::formatParams($params), $opts);
    }

    protected function buildPath($basePath, ...$ids)
    {
        foreach ($ids as $id) {
            if (null === $id || '' === \trim($id)) {
                $msg = 'The resource ID cannot be null or whitespace.';

                throw new \Zone\Wildduck\Exception\InvalidArgumentException($msg);
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
