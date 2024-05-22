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
    public const string OBJECT_NAME = 'asp';
}
