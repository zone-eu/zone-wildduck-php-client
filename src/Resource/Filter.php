<?php

namespace Zone\Wildduck\Resource;

use Zone\Wildduck\FilterAction;
use Zone\Wildduck\FilterQuery;

/**
 * @property string $id
 * @property string $name
 * @property FilterQuery $query
 * @property FilterAction $action
 * @property bool $disabled
 */
class Filter extends ApiResource
{
    public const string OBJECT_NAME = 'filter';
}
