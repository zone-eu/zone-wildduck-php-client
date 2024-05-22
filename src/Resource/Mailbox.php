<?php

namespace Zone\Wildduck\Resource;

use Zone\Wildduck\ApiOperations\All;
use Zone\Wildduck\ApiOperations\Create;
use Zone\Wildduck\ApiOperations\Delete;
use Zone\Wildduck\ApiOperations\NestedResource;
use Zone\Wildduck\ApiOperations\Retrieve;
use Zone\Wildduck\ApiOperations\Update;
use Zone\Wildduck\WildduckObject;

/**
 * @property string $id
 * @property string $name
 * @property string $path
 * @property string|null $specialUse
 * @property int $modifyIndex
 * @property bool $subscribed
 * @property int $total
 * @property int $unseen
 */
class Mailbox extends ApiResource
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

    public const string OBJECT_NAME = 'mailbox';

    public const string PATH_MESSAGES = '/messages';

    public const string SPECIAL_USE_DRAFTS = '\Drafts';

    public const string SPECIAL_USE_JUNK = '\Junk';

    public const string SPECIAL_USE_SENT = '\Sent';

    public const string SPECIAL_USE_TRASH = '\Trash';

    public function messages(array|null $params = null, array|null $opts = null): WildduckObject
    {
        $opts['object'] = Message::OBJECT_NAME;
        return self::_allNestedResources($this->id, static::PATH_MESSAGES, $params, $opts);
    }

    public function isDrafts(): bool
    {
        return $this->specialUse === self::SPECIAL_USE_DRAFTS;
    }

    public function isJunk(): bool
    {
        return $this->specialUse === self::SPECIAL_USE_JUNK;
    }

    public function isSent(): bool
    {
        return $this->specialUse === self::SPECIAL_USE_SENT;
    }

    public function isTrash(): bool
    {
        return $this->specialUse === self::SPECIAL_USE_TRASH;
    }
}
