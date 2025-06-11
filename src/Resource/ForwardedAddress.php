<?php

namespace Zone\Wildduck\Resource;

use Zone\Wildduck\ForwardedAddressLimits;

/**
 * @property string $id
 * @property string $address
 * @property string $name
 * @property string[] $targets
 * @property ForwardedAddressLimits $limits
 * @property Autoreply $autoreply
 * @property string $created
 * @property string[] $tags
 * @property bool $forwardedDisabled
 */
class ForwardedAddress extends ApiResource
{
    public const string OBJECT_NAME = 'forwarded_address';
}
