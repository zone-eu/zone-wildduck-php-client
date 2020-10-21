<?php

namespace Zone\Wildduck;

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

    const OBJECT_NAME = 'mailbox';

    const PATH_MESSAGES = '/messages';

    const SPECIAL_USE_DRAFTS = '\Drafts';
    const SPECIAL_USE_JUNK = '\Junk';
    const SPECIAL_USE_SENT = '\Sent';
    const SPECIAL_USE_TRASH = '\Trash';

    use ApiOperations\All;
    use ApiOperations\Create;
    use ApiOperations\Delete;
    use ApiOperations\NestedResource;
    use ApiOperations\Retrieve;
    use ApiOperations\Update;

    public function messages($params = null, $opts = null)
    {
        $opts['object'] = Message::OBJECT_NAME;
        return self::_allNestedResources($this->id, static::PATH_MESSAGES, $params, $opts);
    }

    public function isDrafts()
    {
        return $this->specialUse === self::SPECIAL_USE_DRAFTS;
    }

    public function isJunk()
    {
        return $this->specialUse === self::SPECIAL_USE_JUNK;
    }

    public function isSent()
    {
        return $this->specialUse === self::SPECIAL_USE_SENT;
    }

    public function isTrash()
    {
        return $this->specialUse === self::SPECIAL_USE_TRASH;
    }
}