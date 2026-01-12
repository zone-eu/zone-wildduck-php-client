<?php

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\Audit\CreateAuditRequestDto;
use Zone\Wildduck\Dto\Audit\AuditResponseDto;
use Zone\Wildduck\Dto\Audit\CreateAuditResponseDto;
use Zone\Wildduck\Dto\Authentication\AuthLogEventResponseDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

class AuditService extends AbstractService
{
    /**
     * @param CreateAuditRequestDto $params
     * @param array<string, mixed>|null $opts
     * @return CreateAuditResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function create(CreateAuditRequestDto $params, array|null $opts = null): CreateAuditResponseDto
    {
        return $this->requestDto('post', '/audit', $params, CreateAuditResponseDto::class, $opts);
    }

    /**
     * @param string $audit
     * @param array<string, mixed>|null $opts
     * @return mixed
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function export(string $audit, array|null $opts = null): mixed
    {
        $opts['raw'] = true;
        return $this->request('get', $this->buildPath('/audit/%s/export.mbox', $audit), null, $opts);
    }

    /**
     * @param string $audit
     * @param array<string, mixed>|null $opts
     * @return AuditResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function get(string $audit, array|null $opts = null): AuditResponseDto
    {
        return $this->requestDto('get', $this->buildPath('/audit/%s', $audit), null, AuditResponseDto::class, $opts);
    }
}
