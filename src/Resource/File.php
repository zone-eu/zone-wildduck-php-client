<?php

namespace Zone\Wildduck;

/**
 * @property string $id
 * @property string $filename
 * @property string $contentType
 * @property int $size
 */
class File extends ApiResource
{
    public const string OBJECT_NAME = 'file';
}
