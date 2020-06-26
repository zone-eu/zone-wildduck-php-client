<?php

namespace Zone\Wildduck;

/**
 * @property string $id
 * @property string $name
 * @property FilterQuery $query
 * @property FilterAction $action
 * @property bool $disabled
 */
class Filter extends ApiResource
{

    const OBJECT_NAME = 'filter';
}
