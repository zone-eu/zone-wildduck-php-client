<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\User;

use Zone\Wildduck\Dto\ResponseDtoInterface;
use Zone\Wildduck\Dto\Shared\KeyInfoResponseDto;
use Zone\Wildduck\Dto\Shared\UserLimitsResponseDto;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * Extended response DTO for detailed user information
 */
readonly class UserInfoResponseDto implements ResponseDtoInterface
{
    /**
     * @param string[] $enabled2fa
     * @param string[] $targets
     * @param string[] $tags
     * @param string[] $fromWhitelist
     * @param string[] $disabledScopes
     */
    public function __construct(
        public bool $success,
        public string $id,
        public string $username,
        public string $name,
        public string $address,
        /** @var string[] */ public array $enabled2fa,
        public bool $autoreply,
        public bool $encryptMessages,
        public bool $encryptForwarded,
        public string $pubKey,
        public KeyInfoResponseDto $keyInfo,
        /** @var array<string, mixed>|null Custom metadata */ public mixed $metaData,
        /** @var array<string, mixed>|null Internal data */ public mixed $internalData,
        /** @var string[] */ public array $targets,
        public int $spamLevel,
        public UserLimitsResponseDto $limits,
        /** @var string[] */ public array $tags,
        /** @var string[] */ public array $fromWhitelist,
        /** @var string[] */ public array $disabledScopes,
        public bool $hasPasswordSet,
        public bool $activated,
        public bool $disabled,
        public bool $suspended,
        public bool $passwordPwned,
        public bool $requirePasswordChange,
        public ?int $retention = null,
        public ?string $mtaRelay = null,
        public ?string $lastPwnedCheck = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['success'])) {
            throw DtoValidationException::missingRequiredField('success', 'bool');
        }
        if (!isset($data['id'])) {
            throw DtoValidationException::missingRequiredField('id', 'string');
        }
        if (!isset($data['username'])) {
            throw DtoValidationException::missingRequiredField('username', 'string');
        }
        if (!isset($data['name'])) {
            throw DtoValidationException::missingRequiredField('name', 'string');
        }
        if (!isset($data['address'])) {
            throw DtoValidationException::missingRequiredField('address', 'string');
        }
        if (!isset($data['enabled2fa'])) {
            throw DtoValidationException::missingRequiredField('enabled2fa', 'array');
        }
        if (!isset($data['autoreply'])) {
            throw DtoValidationException::missingRequiredField('autoreply', 'bool');
        }
        if (!isset($data['encryptMessages'])) {
            throw DtoValidationException::missingRequiredField('encryptMessages', 'bool');
        }
        if (!isset($data['encryptForwarded'])) {
            throw DtoValidationException::missingRequiredField('encryptForwarded', 'bool');
        }
        if (!isset($data['pubKey'])) {
            throw DtoValidationException::missingRequiredField('pubKey', 'string');
        }
        if (!isset($data['keyInfo'])) {
            throw DtoValidationException::missingRequiredField('keyInfo', 'object');
        }
        if (!isset($data['metaData'])) {
            throw DtoValidationException::missingRequiredField('metaData', 'object');
        }
        if (!isset($data['internalData'])) {
            throw DtoValidationException::missingRequiredField('internalData', 'object');
        }
        if (!isset($data['targets'])) {
            throw DtoValidationException::missingRequiredField('targets', 'array');
        }
        if (!isset($data['spamLevel'])) {
            throw DtoValidationException::missingRequiredField('spamLevel', 'int');
        }
        if (!isset($data['limits'])) {
            throw DtoValidationException::missingRequiredField('limits', 'object');
        }
        if (!isset($data['tags'])) {
            throw DtoValidationException::missingRequiredField('tags', 'array');
        }
        if (!isset($data['fromWhitelist'])) {
            throw DtoValidationException::missingRequiredField('fromWhitelist', 'array');
        }
        if (!isset($data['disabledScopes'])) {
            throw DtoValidationException::missingRequiredField('disabledScopes', 'array');
        }
        if (!isset($data['hasPasswordSet'])) {
            throw DtoValidationException::missingRequiredField('hasPasswordSet', 'bool');
        }
        if (!isset($data['activated'])) {
            throw DtoValidationException::missingRequiredField('activated', 'bool');
        }
        if (!isset($data['disabled'])) {
            throw DtoValidationException::missingRequiredField('disabled', 'bool');
        }
        if (!isset($data['suspended'])) {
            throw DtoValidationException::missingRequiredField('suspended', 'bool');
        }
        if (!isset($data['passwordPwned'])) {
            throw DtoValidationException::missingRequiredField('passwordPwned', 'bool');
        }
        if (!isset($data['requirePasswordChange'])) {
            throw DtoValidationException::missingRequiredField('requirePasswordChange', 'bool');
        }

        return new self(
            success: $data['success'],
            id: $data['id'],
            username: $data['username'],
            name: $data['name'],
            address: $data['address'],
            enabled2fa: $data['enabled2fa'],
            autoreply: $data['autoreply'],
            encryptMessages: $data['encryptMessages'],
            encryptForwarded: $data['encryptForwarded'],
            pubKey: $data['pubKey'],
            keyInfo: KeyInfoResponseDto::fromArray($data['keyInfo']),
            metaData: $data['metaData'],
            internalData: $data['internalData'],
            targets: $data['targets'],
            spamLevel: $data['spamLevel'],
            limits: UserLimitsResponseDto::fromArray($data['limits']),
            tags: $data['tags'],
            fromWhitelist: $data['fromWhitelist'],
            disabledScopes: $data['disabledScopes'],
            hasPasswordSet: $data['hasPasswordSet'],
            activated: $data['activated'],
            disabled: $data['disabled'],
            suspended: $data['suspended'],
            passwordPwned: $data['passwordPwned'],
            requirePasswordChange: $data['requirePasswordChange'],
            retention: $data['retention'] ?? null,
            mtaRelay: $data['mtaRelay'] ?? null,
            lastPwnedCheck: $data['lastPwnedCheck'] ?? null,
        );
    }
}
