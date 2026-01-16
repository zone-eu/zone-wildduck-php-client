<?php

declare(strict_types=1);

namespace Zone\Wildduck\Dto\User;

use Zone\Wildduck\Dto\RequestDtoInterface;
use Zone\Wildduck\Dto\Traits\EncryptionSupportTrait;
use Zone\Wildduck\Dto\Traits\MetaDataSupportTrait;
use Zone\Wildduck\Dto\Traits\TaggableTrait;
use Zone\Wildduck\Exception\DtoValidationException;

/**
 * DTO for creating a new user
 */
class CreateUserRequestDto implements RequestDtoInterface
{
    use EncryptionSupportTrait;
    use MetaDataSupportTrait;
    use TaggableTrait;

    /**
     * @param string[] $tags
     * @param string[] $addTagsToAddress
     * @param string[] $fromWhitelist
     * @param string[] $disabledScopes
     */
    public function __construct(
        public string $username,
        public string $password,
        public ?string $name = null,
        public ?string $address = null,
        public ?bool $emptyAddress = null,
        public ?int $retention = null,
        public ?bool $encryptMessages = null,
        public ?bool $encryptForwarded = null,
        public ?string $pubKey = null,
        public ?string $language = null,
        /** @var string[]|null */ public ?array $targets = null,
        public ?int $spamLevel = null,
        public ?int $quota = null,
        public ?int $recipients = null,
        public ?int $forwards = null,
        public ?int $imapMaxUpload = null,
        public ?int $imapMaxDownload = null,
        public ?int $pop3MaxDownload = null,
        public ?int $pop3MaxMessages = null,
        public ?int $imapMaxConnections = null,
        public ?int $receivedMax = null,
        /** @var string[] */ public array $tags = [],
        /** @var string[] */ public array $addTagsToAddress = [],
        /** @var string[] */ public array $fromWhitelist = [],
        public ?bool $requirePasswordChange = null,
        /** @var array<string, mixed>|null Custom metadata */ public ?array $metaData = null,
        /** @var array<string, mixed>|null Internal data */ public ?array $internalData = null,
        /** @var string[] */ public array $disabledScopes = [],
        public ?bool $disabled = null,
        public ?bool $suspended = null,
        public ?string $featureFlags = null,
    ) {
        if (empty($username)) {
            throw DtoValidationException::missingRequiredField('username', 'string');
        }
        if (empty($password)) {
            throw DtoValidationException::missingRequiredField('password', 'string');
        }
    }

    public function toArray(): array
    {
        $data = [
            'username' => $this->username,
            'password' => $this->password,
        ];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }
        if ($this->address !== null) {
            $data['address'] = $this->address;
        }
        if ($this->emptyAddress !== null) {
            $data['emptyAddress'] = $this->emptyAddress;
        }
        if ($this->retention !== null) {
            $data['retention'] = $this->retention;
        }
        if ($this->pubKey !== null) {
            $data['pubKey'] = $this->pubKey;
        }
        if ($this->language !== null) {
            $data['language'] = $this->language;
        }
        if ($this->targets !== null) {
            $data['targets'] = $this->targets;
        }
        if ($this->spamLevel !== null) {
            $data['spamLevel'] = $this->spamLevel;
        }
        if ($this->quota !== null) {
            $data['quota'] = $this->quota;
        }
        if ($this->recipients !== null) {
            $data['recipients'] = $this->recipients;
        }
        if ($this->forwards !== null) {
            $data['forwards'] = $this->forwards;
        }
        if ($this->imapMaxUpload !== null) {
            $data['imapMaxUpload'] = $this->imapMaxUpload;
        }
        if ($this->imapMaxDownload !== null) {
            $data['imapMaxDownload'] = $this->imapMaxDownload;
        }
        if ($this->pop3MaxDownload !== null) {
            $data['pop3MaxDownload'] = $this->pop3MaxDownload;
        }
        if ($this->pop3MaxMessages !== null) {
            $data['pop3MaxMessages'] = $this->pop3MaxMessages;
        }
        if ($this->receivedMax !== null) {
            $data['receivedMax'] = $this->receivedMax;
        }
        if (!empty($this->disabledScopes)) {
            $data['disabledScopes'] = $this->disabledScopes;
        }
        if (!empty($this->addTagsToAddress)) {
            $data['addTagsToAddress'] = $this->addTagsToAddress;
        }
        if (!empty($this->fromWhitelist)) {
            $data['fromWhitelist'] = $this->fromWhitelist;
        }
        if ($this->requirePasswordChange !== null) {
            $data['requirePasswordChange'] = $this->requirePasswordChange;
        }
        if ($this->disabled !== null) {
            $data['disabled'] = $this->disabled;
        }
        if ($this->suspended !== null) {
            $data['suspended'] = $this->suspended;
        }
        if ($this->featureFlags !== null) {
            $data['featureFlags'] = $this->featureFlags;
        }

        return array_merge($data, $this->getEncryptionArray(), $this->getMetaDataArray(), $this->getTagsArray());
    }
}
