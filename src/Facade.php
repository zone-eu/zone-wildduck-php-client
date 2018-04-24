<?php


namespace Wildduck;

class Facade extends \Illuminate\Support\Facades\Facade
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