<?php

declare(strict_types=1);

namespace Tests\Integration\Service;

use Tests\Integration\IntegrationTestCase;
use Zone\Wildduck\Dto\Settings\CreateOrUpdateSettingRequestDto;
use Zone\Wildduck\Dto\Settings\SettingResponseDto;
use Zone\Wildduck\Dto\PaginatedResultDto;
use Zone\Wildduck\Dto\Settings\SettingDto;
use Zone\Wildduck\Dto\Settings\SettingsListResponseDto;
use Zone\Wildduck\Exception\ValidationException;

class SettingsServiceIntegrationTest extends IntegrationTestCase
{
    private ?string $createdSettingKey = null;

    protected function tearDown(): void
    {
        if ($this->createdSettingKey !== null) {
            // Note: Settings service might not have a delete method
            // Settings are typically persistent configuration
            // We just clean up the reference
            $this->createdSettingKey = null;
        }

        parent::tearDown();
    }

    public function testListAllSettings(): void
    {
        // List all settings
        $settings = $this->client->settings()->all();

        $this->assertInstanceOf(SettingsListResponseDto::class, $settings);

        // Settings list may be empty or contain default settings
        if (count($settings->settings) > 0) {
            foreach ($settings->settings as $setting) {
                $this->assertInstanceOf(SettingDto::class, $setting);
                $this->assertNotEmpty($setting->key);
            }
        }
    }

    public function testCreateOrUpdateAndGetSetting(): void
    {
        // Create or update a setting
        $settingKey = 'const:max:recipients';
        $this->createdSettingKey = $settingKey;

        $createDto = new CreateOrUpdateSettingRequestDto(
            value: '333'
        );

        $createResult = $this->client->settings()->createOrUpdate($settingKey, $createDto);

        $this->assertInstanceOf(SettingResponseDto::class, $createResult);
        $this->assertEquals($settingKey, $createResult->key);

        // Get the specific setting
        $setting = $this->client->settings()->get($settingKey);

        $this->assertInstanceOf(SettingResponseDto::class, $setting);
        $this->assertEquals($settingKey, $setting->key);
        $this->assertEquals('333', $setting->value);

        // Update the setting with a new value
        $updateDto = new CreateOrUpdateSettingRequestDto(
            value: '456'
        );

        $updateResult = $this->client->settings()->createOrUpdate($settingKey, $updateDto);

        $this->assertInstanceOf(SettingResponseDto::class, $updateResult);
        $this->assertEquals($settingKey, $updateResult->key);

        // Verify the update by getting the setting again
        $updatedSetting = $this->client->settings()->get($settingKey);

        $this->assertEquals('456', $updatedSetting->value);
    }
}
