<?php

namespace Zone\Wildduck;

// FIXME: Unable to use typed class properties since PHP doesn't understand that they are initialized by WildduckObject __get/__set?

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
 * @property string $fromWhitelist
 * @property array $disabledScopes
 * @property bool $hasPasswordSet
 * @property bool $activated
 * @property bool $disabled
 * @property bool $suspended
 */
class User extends ApiResource
{
    const OBJECT_NAME = 'user';

    const PATH_ADDRESSES = '/addresses';
    const PATH_MAILBOXES = '/mailboxes';

    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\Delete;
    use ApiOperations\NestedResource;
    use ApiOperations\Retrieve;
    use ApiOperations\Update;

    public function addresses($params = null, $opts = null)
    {
        $opts['object'] = Address::OBJECT_NAME;
        return self::_allNestedResources($this->id, static::PATH_ADDRESSES, $params, $opts);
    }

    public function mailboxes($params = null, $opts = null)
    {
        $opts['object'] = Mailbox::OBJECT_NAME;
        return self::_allNestedResources($this->id, static::PATH_MAILBOXES, $params, $opts);
    }
}
