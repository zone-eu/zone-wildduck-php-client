<?php

namespace Zone\Wildduck\Service;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Zone\Wildduck\ApiResponse;
use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Dto\PaginatedResultDto;
use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidArgumentException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
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
     * Make a request and convert response to DTO
     *
     * @template T of ResponseDtoInterface
     * @param string $method
     * @param string $path
     * @param RequestDtoInterface|null $params
     * @param class-string<T> $responseClass
     * @param array|null $opts
     * @return T
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    protected function requestDto(
        string $method,
        string $path,
        RequestDtoInterface|null $params,
        string $responseClass,
        array|null $opts = null
    ): ResponseDtoInterface {
        $response = $this->request($method, $path, $params, $opts);
        return $responseClass::fromArray($response);
    }

    /**
     * Make a request and convert paginated response to PaginatedResultDto
     *
     * @template T of ResponseDtoInterface
     * @param string $method
     * @param string $path
     * @param RequestDtoInterface|null $params
     * @param class-string<T> $itemClass
     * @param array|null $opts
     * @return PaginatedResultDto<T>
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws InvalidDatabaseException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    protected function requestPaginatedDto(
        string $method,
        string $path,
        RequestDtoInterface|null $params,
        string $itemClass,
        array|null $opts = null
    ): PaginatedResultDto {
        $response = $this->request($method, $path, $params, $opts);
        /** @var PaginatedResultDto<T> */
        return PaginatedResultDto::fromArray($response, $itemClass);
    }

    /**
     * Make a request and return array response
     *
     * @param string $method
     * @param string $path
     * @param RequestDtoInterface|null $params
     * @param array|null $opts
     * @return array<string, mixed>
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    protected function request(string $method, string $path, RequestDtoInterface|null $params, array|null $opts = null): array
    {
        $opts = $opts ?? [];
        $paramsArray = $params?->toArray();

        $response = $this->getClient()->request($method, $path, $paramsArray, $opts, false);

        return $response->json ?? [];
    }

    /**
     * Make a request and return raw
     *
     * @param string $method
     * @param string $path
     * @param RequestDtoInterface|null $params
     * @param array|null $opts
     * @return ApiResponse|string
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    protected function requestResponse(string $method, string $path, RequestDtoInterface|null $params, array|null $opts = null): ApiResponse|string
    {
        $opts = $opts ?? [];
        $paramsArray = $params?->toArray();

        $response = $this->getClient()->request($method, $path, $paramsArray, $opts, false);

        return $response;
    }

    /**
     * Upload file
     *
     * @param string $method
     * @param string $path
     * @param string $fileContent
     * @param array|null $opts
     *
     * @return ApiResponse|string
     *
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function uploadFile(string $method, string $path, string $fileContent, array|null $opts): ApiResponse|string
    {
        return $this->getClient()->request($method, $path, $fileContent, $opts, true);
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

    /**
     * Stream a request to the API (for EventSource streams)
     *
     * @param string $method
     * @param string $path
     * @param array|null $params
     * @param array|null $opts
     * @return StreamedResponse
     */
    protected function stream(string $method, string $path, array|null $params = null, array|null $opts = null): StreamedResponse
    {
        return $this->client->stream($method, $path, $params, $opts);
    }
}
