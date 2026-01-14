<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\User;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Dto\Shared\KeyInfoResponseDto;
use Zone\Wildduck\Dto\Shared\UserLimitsResponseDto;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * User DTO representing a WildDuck user account
 */
readonly class UserResponseDto implements ResponseDtoInterface
{
    /**
     * @param string[] $enabled2fa
     * @param string[] $targets
     * @param string[] $tags
     * @param string[] $fromWhitelist
     * @param string[] $disabledScopes
     */
    public function __construct(
        public string $id,
        public string $username,
        public ?string $name = null,
        public ?string $address = null,
        public int|bool|null $retention = null,
        /** @var string[] */
        public array $enabled2fa = [],
        public ?bool $autoreply = null,
        public ?bool $encryptMessages = null,
        public ?bool $encryptForwarded = null,
        public ?string $pubKey = null,
        public ?KeyInfoResponseDto $keyInfo = null,
        /** @var array<string, mixed>|null Custom metadata */
        public ?array $metaData = null,
        /** @var string[] */
        public array $targets = [],
        public ?int $spamLevel = null,
        public ?UserLimitsResponseDto $limits = null,
        /** @var string[] */
        public array $tags = [],
        /** @var string[] */
        public array $fromWhitelist = [],
        /** @var string[] */
        public array $disabledScopes = [],
        public ?bool $hasPasswordSet = null,
        public ?bool $activated = null,
        public ?bool $disabled = null,
        public ?bool $suspended = null,
        public ?bool $passwordPwned = null,
        public ?bool $requirePasswordChange = null,
    ) {}

    public static function fromArray(array $data): self
    {
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'string');
        }
        if (!isset($data['username'])) {
            throw DtoValidationException::missingRequiredField('username', 'string');
        }

        if (!is_string($data['id'])) {
            throw DtoValidationException::invalidType('id', 'string', $data['id']);
        }
        if (!is_string($data['username'])) {
            throw DtoValidationException::invalidType('username', 'string', $data['username']);
        }

        $keyInfo = null;
        if (isset($data['keyInfo']) && is_array($data['keyInfo'])) {
            $keyInfo = KeyInfoResponseDto::fromArray($data['keyInfo']);
        }

        $limits = null;
        if (isset($data['limits']) && is_array($data['limits'])) {
            $limits = UserLimitsResponseDto::fromArray($data['limits']);
        }

        $metaData = null;
        if (isset($data['metaData']) && is_array($data['metaData'])) {
            $metaData = $data['metaData'];
        }

        return new self(
            id: $data['id'],
            username: $data['username'],
            name: isset($data['name']) && is_string($data['name']) ? $data['name'] : null,
            address: isset($data['address']) && is_string($data['address']) ? $data['address'] : null,
            retention: $data['retention'] ?? null,
            enabled2fa: isset($data['enabled2fa']) && is_array($data['enabled2fa']) ? $data['enabled2fa'] : [],
            autoreply: isset($data['autoreply']) && is_bool($data['autoreply']) ? $data['autoreply'] : null,
            encryptMessages: isset($data['encryptMessages']) && is_bool($data['encryptMessages']) ? $data['encryptMessages'] : null,
            encryptForwarded: isset($data['encryptForwarded']) && is_bool($data['encryptForwarded']) ? $data['encryptForwarded'] : null,
            pubKey: isset($data['pubKey']) && is_string($data['pubKey']) ? $data['pubKey'] : null,
            keyInfo: $keyInfo,
            metaData: $metaData,
            targets: isset($data['targets']) && is_array($data['targets']) ? $data['targets'] : [],
            spamLevel: isset($data['spamLevel']) && is_int($data['spamLevel']) ? $data['spamLevel'] : null,
            limits: $limits,
            tags: isset($data['tags']) && is_array($data['tags']) ? $data['tags'] : [],
            fromWhitelist: isset($data['fromWhitelist']) && is_array($data['fromWhitelist']) ? $data['fromWhitelist'] : [],
            disabledScopes: isset($data['disabledScopes']) && is_array($data['disabledScopes']) ? $data['disabledScopes'] : [],
            hasPasswordSet: isset($data['hasPasswordSet']) && is_bool($data['hasPasswordSet']) ? $data['hasPasswordSet'] : null,
            activated: isset($data['activated']) && is_bool($data['activated']) ? $data['activated'] : null,
            disabled: isset($data['disabled']) && is_bool($data['disabled']) ? $data['disabled'] : null,
            suspended: isset($data['suspended']) && is_bool($data['suspended']) ? $data['suspended'] : null,
            passwordPwned: isset($data['passwordPwned']) && is_bool($data['passwordPwned']) ? $data['passwordPwned'] : null,
            requirePasswordChange: isset($data['requirePasswordChange']) && is_bool($data['requirePasswordChange']) ? $data['requirePasswordChange'] : null,
        );
    }
}
