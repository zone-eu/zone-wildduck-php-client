<?php

namespace Zone\Wildduck\Resource;

use Zone\Wildduck\ApiOperations\All;
use Zone\Wildduck\ApiOperations\Create;
use Zone\Wildduck\ApiOperations\Delete;
use Zone\Wildduck\ApiOperations\Retrieve;
use Zone\Wildduck\ApiOperations\Update;

/**
 * @property string $id
 * @property string $name
 * @property string $address
 * @property string[] $tags
 * @property string $user
 * @property bool $main
 * @property string $created
 * @property bool $forwarded
 * @property bool $forwardedDisabled
 * @property string[] $target
 */
class Address extends ApiResource
{
	/**
	 * @Deprecated Traits All, Delete, Create, Retrieve, Update
	 */
    use All;
	use Delete;
    use Create;
    use Retrieve;
    use Update;

    public const string OBJECT_NAME = 'address';
}
