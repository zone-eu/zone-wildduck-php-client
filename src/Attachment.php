<?php

namespace Zone\Wildduck;

/**
 * @property string $id
 * @property string $filename
 * @property string $contentType
 * @property string $disposition
 * @property string $transferEncoding
 * @property bool $related
 * @property int $sizeKb
 */
class Attachment extends WildduckObject // TODO: ApiResource?
{

    const OBJECT_NAME = 'attachment';

    // TODO: download()
}