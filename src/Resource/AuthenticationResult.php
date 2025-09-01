<?php

namespace Zone\Wildduck\Resource;

use Zone\Wildduck\ApiOperations\All;
use Zone\Wildduck\ApiOperations\Create;
use Zone\Wildduck\ApiOperations\Delete;
use Zone\Wildduck\ApiOperations\NestedResource;
use Zone\Wildduck\ApiOperations\Retrieve;
use Zone\Wildduck\ApiOperations\Update;
use Zone\Wildduck\KeyInfo;
use Zone\Wildduck\UserLimits;
use Zone\Wildduck\WildduckObject;

/**
 * @property bool $success Indicates successful response
 * @property string $id ID of the User
 * @property string $username Username of authenticated User
 * @property string $address Default email address of authenticated User
 * @property string $scope The scope this authentication is valid for
 * @property string[] $require2fa List of enabled 2FA mechanisms
 */
class AuthenticationResult extends ApiResource
{
    use NestedResource;

    public const string OBJECT_NAME = 'authenticationResult';
}
