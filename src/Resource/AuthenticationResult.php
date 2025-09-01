<?php

namespace Zone\Wildduck\Resource;


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
    public const string OBJECT_NAME = 'authenticationResult';
}
