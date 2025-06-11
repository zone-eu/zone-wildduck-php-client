<?php

namespace Zone\Wildduck\Resource;

/**
 * @property bool $status
 * @property string $name
 * @property string $subject
 * @property string $html
 * @property string $text
 * @property string $start
 * @property string $end
 */
class Autoreply extends ApiResource
{
    public const string OBJECT_NAME = 'autoreply';
}
