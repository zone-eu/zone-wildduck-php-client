<?php

namespace Zone\Wildduck\Service;

use Zone\Wildduck\WildduckClientInterface;

/**
 * Abstract base class for all service factories used to expose service
 * instances through {@link \Zone\Wildduck\WildduckClient}.
 *
 * Service factories serve two purposes:
 *
 * 1. Expose properties for all services through the `__get()` magic method.
 * 2. Lazily initialize each service instance the first time the property for
 *    a given service is used.
 */
abstract class AbstractServiceFactory
{
    /** @var array<string, AbstractService|AbstractServiceFactory> */
    private array $services = [];

    /**
     * @param WildduckClientInterface $client
     */
    public function __construct(private readonly WildduckClientInterface $client)
    {
    }

    /**
     * @param string $name
     *
     * @return null|string
     */
    abstract protected function getServiceClass(string $name):  null|string;

    /**
     * @return null|AbstractService|AbstractServiceFactory
     */
    public function __get(mixed $name)
    {
        $serviceClass = $this->getServiceClass($name);
        if (null !== $serviceClass) {
            if (!array_key_exists($name, $this->services)) {
                $this->services[$name] = new $serviceClass($this->client);
            }

            return $this->services[$name];
        }

        trigger_error('Undefined property: ' . static::class . '::$' . $name);

        return null;
    }
}
