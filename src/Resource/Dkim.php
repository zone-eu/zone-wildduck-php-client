<?php

namespace Zone\Wildduck;

/**
 * @property string $id
 * @property string $domain
 * @property string $selector
 * @property string $description
 * @property string $fingerprint
 * @property string $publicKey
 * @property object $dnsText
 * @property string $created
 */
class Dkim extends ApiResource
{
    public const string OBJECT_NAME = 'dkim';
}
