<?php

namespace Zone\Wildduck\Resource;

/**
 * @property string $id
 * @property string $alias
 * @property string $domain
 * @property string $created
 */
class DomainAlias extends ApiResource
{
    public const string OBJECT_NAME = 'domain_alias';
}
