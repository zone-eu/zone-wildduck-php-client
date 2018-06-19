<?php

namespace Wildduck\Facades;

use Illuminate\Support\Facades\Facade;
use Wildduck\Client;

class Wildduck extends Facade
{
    /**
     * @return Client
     */
    protected static function getFacadeAccessor()
    {
        return Client::instance()
            ->setHost(config('wildduck.host'))
            ->setDebug(config('wildduck.debug'));
    }
}