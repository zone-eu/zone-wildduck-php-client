<?php

namespace Wildduck;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/wildduck.php' => config_path('wildduck.php')
        ]);
    }
}