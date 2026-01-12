<?php

declare(strict_types=1);

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\Export\CreateExportRequestDto;
use Zone\Wildduck\Dto\Export\ExportResponseDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

/**
 * Export service for data export/import operations
 */
class ExportService extends AbstractService
{
    /**
     * Export user data
     *
     * @param CreateExportRequestDto $params
     * @param array|null $opts
     * @return ExportResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function export(CreateExportRequestDto $params, array|null $opts = null): ExportResponseDto
    {
        return $this->requestDto('post', '/data/export', $params, ExportResponseDto::class, $opts);
    }

    /**
     * Import user data
     *
     * @param array|null $opts
     * @return ExportResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function import(array|null $opts = null): ExportResponseDto
    {
        return $this->requestDto('post', '/data/import', null, ExportResponseDto::class, $opts);
    }
}
