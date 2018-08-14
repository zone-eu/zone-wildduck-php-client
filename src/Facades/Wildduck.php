<?php

namespace Wildduck\Facades;

use Illuminate\Support\Facades\Facade;
use Wildduck\Api\Addresses;
use Wildduck\Api\ApplicationPasswords;
use Wildduck\Api\Authentication;
use Wildduck\Api\Autoreplies;
use Wildduck\Api\Filters;
use Wildduck\Api\Mailboxes;
use Wildduck\Api\Messages;
use Wildduck\Api\Submission;
use Wildduck\Api\TwoFactorAuth;
use Wildduck\Api\Users;
use Wildduck\Client;

/**
 * @method static Addresses addresses()
 * @method static ApplicationPasswords applicationPasswords()
 * @method static Authentication authentication()
 * @method static Autoreplies autoreplies()
 * @method static Filters filters()
 * @method static Mailboxes mailboxes()
 * @method static Messages messages()
 * @method static Submission submission()
 * @method static TwoFactorAuth twoFactorAuth()
 * @method static Users users()
 */
class Wildduck extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return Client::instance()
            ->setHost(config('wildduck.host'))
            ->setDebug(config('wildduck.debug'));
    }
}