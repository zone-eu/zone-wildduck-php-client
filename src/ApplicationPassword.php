<?php

namespace Zone\Wildduck;

/**
 * @property string $id
 * @property string $description
 * @property string[] $scopes
 * @property ApplicationPasswordLastUse $lastUse
 * @property string $created
 */
class ApplicationPassword extends ApiResource
{

    const OBJECT_NAME = 'asp';
}