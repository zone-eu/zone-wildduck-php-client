<?php

namespace Zone\Wildduck\Resource;


/**
 * @property bool $success Indicates successful response
 * @property string $id ID of the User
 * @property string $username Username of authenticated User
 * @property string $address Default email address of authenticated User
 * @property string $scope The scope this authentication is valid for
 * @property bool|string[] $require2fa List of enabled 2FA mechanisms
 * @property bool $requirePasswordChange Indicates if account password has been reset and should be replaced
 * @property string $token If access token was requested then this is the value to use as access token when making API requests on behalf of logged in user
 */
class AuthenticationResult extends ApiResource
{
    public const string OBJECT_NAME = 'authenticationResult';
}
