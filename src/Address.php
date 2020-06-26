<?php

namespace Zone\Wildduck;

/**
 * @property string $id
 * @property string $name
 * @property string $address
 * @property string $user
 * @property bool $main
 * @property string $created
 * @property bool $forwarded
 * @property bool $forwardedDisabled
 * @property string[] $target
 */
class Address extends ApiResource
{

    use ApiOperations\All;
    use ApiOperations\Delete;
    use ApiOperations\Create;
    use ApiOperations\Retrieve;
    use ApiOperations\Update;

    const OBJECT_NAME = 'address';
}