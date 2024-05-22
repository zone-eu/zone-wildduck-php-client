<?php

namespace Zone\Wildduck;

use Zone\Wildduck\ApiOperations\All;
use Zone\Wildduck\ApiOperations\Create;
use Zone\Wildduck\ApiOperations\Delete;
use Zone\Wildduck\ApiOperations\NestedResource;
use Zone\Wildduck\ApiOperations\Retrieve;
use Zone\Wildduck\ApiOperations\Update;

/**
 * @property string $id
 * @property string $username
 * @property string $name
 * @property string $address
 * @property int|bool $retention
 * @property array $enabled2fa
 * @property bool $autoreply
 * @property bool $encryptMessages
 * @property bool $encryptForwarded
 * @property string $pubKey
 * @property KeyInfo $keyInfo
 * @property object $metaData
 * @property array $targets
 * @property int $spamLevel
 * @property UserLimits $limits
 * @property array $tags
 * @property string[] $fromWhitelist
 * @property array $disabledScopes
 * @property bool $hasPasswordSet
 * @property bool $activated
 * @property bool $disabled
 * @property bool $suspended
 */
class User extends ApiResource
{
	/**
	 * @deprecated
	 */
	use All;
	use Create;
	use Delete;
	use Retrieve;
	use Update;

	use NestedResource;

    public const string OBJECT_NAME = 'user';

    public const string PATH_ADDRESSES = '/addresses';

    public const string PATH_MAILBOXES = '/mailboxes';

    protected function addresses(array|null $params = null, array|null $opts = null): WildduckObject
    {
        $opts['object'] = Address::OBJECT_NAME;
        return self::_allNestedResources($this->id, static::PATH_ADDRESSES, $params, $opts);
    }

	protected function mailboxes(array|null $params = null, array|null $opts = null): WildduckObject
    {
        $opts['object'] = Mailbox::OBJECT_NAME;
        return self::_allNestedResources($this->id, static::PATH_MAILBOXES, $params, $opts);
    }
}
