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
 * @property string $id ID of the Mailbox (required)
 * @property string $name Name for the mailbox (unicode string) (required)
 * @property string $path Full path of the mailbox, folders are separated by slashes, ends with the mailbox name (unicode string) (required)
 * @property string|null $specialUse Either special use identifier or null. One of Drafts, Junk, Sent or Trash (required)
 * @property int $modifyIndex Modification sequence number. Incremented on every change in the mailbox (required)
 * @property bool $subscribed Mailbox subscription status. IMAP clients may unsubscribe from a folder (required)
 * @property bool $hidden Is the folder hidden or not (required)
 * @property int $total How many messages are stored in this mailbox (required)
 * @property int $unseen How many unseen messages are stored in this mailbox (required)
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
