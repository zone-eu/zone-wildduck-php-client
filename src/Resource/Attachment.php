<?php

namespace Zone\Wildduck\Resource;

/**
 * @property string $id
 * @property string $filename
 * @property string $contentType
 * @property string $disposition
 * @property string $transferEncoding
 * @property bool $related
 * @property int $sizeKb
 */
class Attachment extends ApiResource
{
    public const string OBJECT_NAME = 'attachment';
}
