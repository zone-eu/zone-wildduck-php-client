<?php

declare(strict_types=1);

namespace Zone\Wildduck\Service;

use Zone\Wildduck\Dto\PaginatedResultDto;
use Zone\Wildduck\Dto\Settings\CreateOrUpdateSettingRequestDto;
use Zone\Wildduck\Dto\Settings\ListSettingsRequestDto;
use Zone\Wildduck\Dto\Settings\SettingResponseDto;
use Zone\Wildduck\Dto\Settings\SettingsListResponseDto;
use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;

/**
 * Settings service for managing server settings
 */
class SettingsService extends AbstractService
{
    /**
     * Get all settings
     *
     * @param ListSettingsRequestDto|null $params Query parameters
     * @param array|null $opts
     * @return SettingsListResponseDto Settings response (non-standard pagination)
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function all(?ListSettingsRequestDto $params = null, array|null $opts = null): SettingsListResponseDto
    {
        return $this->requestDto('get', '/settings', $params, SettingsListResponseDto::class, $opts);
    }

    /**
     * Get a specific setting by key
     *
     * @param string $key
     * @param array|null $opts
     * @return SettingResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function get(string $key, array|null $opts = null): SettingResponseDto
    {
        return $this->requestDto('get', $this->buildPath('/settings/%s', $key), null, SettingResponseDto::class, $opts);
    }

    /**
     * Create or update a setting
     *
     * @param string $key
     * @param CreateOrUpdateSettingRequestDto $params
     * @param array|null $opts
     * @return SettingResponseDto
     * @throws ApiConnectionException
     * @throws AuthenticationFailedException
     * @throws InvalidAccessTokenException
     * @throws RequestFailedException
     * @throws ValidationException
     */
    public function createOrUpdate(string $key, CreateOrUpdateSettingRequestDto $params, array|null $opts = null): SettingResponseDto
    {
        return $this->requestDto('post', $this->buildPath('/settings/%s', $key), $params, SettingResponseDto::class, $opts);
    }
}
