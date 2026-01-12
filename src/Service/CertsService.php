<?php

declare(strict_types=1);

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\Certs\CertificateListRequestDto;
use Zone\Wildduck\Dto\Certs\CertificateCreateOrUpdateResponseDto;
use Zone\Wildduck\Dto\Certs\CertificateInformationResponseDto;
use Zone\Wildduck\Dto\Certs\CertificateListResponseDto;
use Zone\Wildduck\Dto\Certs\CertificateResolveResponseDto;
use Zone\Wildduck\Dto\Certs\CertificateResponseDto;
use Zone\Wildduck\Dto\Certs\CreateOrUpdateCertificateRequestDto;
use Zone\Wildduck\Dto\PaginatedResultDto;
use Zone\Wildduck\Dto\Shared\SuccessResponseDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

/**
 * Certs service for managing TLS certificates
 */
class CertsService extends AbstractService
{
    /**
     * Get all certificates
     *
     * @param CertificateListRequestDto $params Query parameters
     * @param array|null $opts
     * @return PaginatedResultDto<CertificateListResponseDto>
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function all(CertificateListRequestDto $params, array|null $opts = null): PaginatedResultDto
    {
        return $this->requestPaginatedDto('get', '/certs', $params, CertificateListResponseDto::class, $opts);
    }

    /**
     * Create a new certificate
     *
     * @param CreateOrUpdateCertificateRequestDto $params
     * @param array|null $opts
     * @return CertificateCreateOrUpdateResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function create(CreateOrUpdateCertificateRequestDto $params, array|null $opts = null): CertificateCreateOrUpdateResponseDto
    {
        return $this->requestDto('post', '/certs', $params, CertificateCreateOrUpdateResponseDto::class, $opts);
    }

    /**
     * Get a specific certificate
     *
     * @param string $cert Certificate ID
     * @param array|null $opts
     * @return CertificateInformationResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function get(string $cert, array|null $opts = null): CertificateInformationResponseDto
    {
        return $this->requestDto('get', $this->buildPath('/certs/%s', $cert), null, CertificateInformationResponseDto::class, $opts);
    }

    /**
     * Delete a certificate
     *
     * @param string $cert Certificate ID
     * @param array|null $opts
     * @return SuccessResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function delete(string $cert, array|null $opts = null): SuccessResponseDto
    {
        return $this->requestDto('delete', $this->buildPath('/certs/%s', $cert), null, SuccessResponseDto::class, $opts);
    }

    /**
     * Resolve certificate by servername
     *
     * @param string $servername Server name
     * @param array|null $opts
     * @return CertificateResolveResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function resolve(string $servername, array|null $opts = null): CertificateResolveResponseDto
    {
        return $this->requestDto('get', $this->buildPath('/certs/resolve/%s', $servername), null, CertificateResolveResponseDto::class, $opts);
    }
}
