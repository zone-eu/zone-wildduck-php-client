<?php

namespace Zone\Wildduck\Resource;

/**
 * @property string $id File ID (required)
 * @property string|bool $filename Filename. False if none (required)
 * @property string|bool $contentType Content-Type of the file. False if none (required)
 * @property string|null $cid Content ID
 * @property int $size File size (required)
 * @property string $created Created datestring (required)
 * @property string $md5 md5 hash (required)
 */
class File extends ApiResource
{
    public const string OBJECT_NAME = 'file';
}
