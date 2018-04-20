<?php

require_once __DIR__ . '/vendor/autoload.php';

use Wildduck\Client;

Client::authentication()->authenticate([
    'username' => 'ivan',
    'password' => 'Asd123',
]);
