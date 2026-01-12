<?php

declare(strict_types=1);

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\Health\HealthCheckResponseDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

/**
 * Health service for checking API health status
 */
class HealthService extends AbstractService
{
    /**
     * Check health status of the API
     *
     * @param array|null $opts
     * @return HealthCheckResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function check(array|null $opts = null): HealthCheckResponseDto
    {
        return $this->requestDto('get', '/health', null, HealthCheckResponseDto::class, $opts);
    }
}
