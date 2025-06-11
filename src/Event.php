<?php

namespace Zone\Wildduck;

/**
 * @property string $id
 * @property string $action
 * @property string $result
 * @property string $sess
 * @property string $ip
 * @property string $created
 */
class Event extends WildduckObject
{
    public const string OBJECT_NAME = 'event';
}
