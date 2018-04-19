<?php

$uri1 = '/users/:user/address';
$uri2 = '/users/:user/address/:address';

$args1 = [
    'user' => '13fj29g2',
];

$args2 = [
    'user' => '323jv952m3bjv9',
//    'address' => '209vny2vmfj034',
];

$uri2 = preg_replace_callback('/(:[a-z]+)/', function ($matches) use ($args2) {
    echo "Callback\n";
    $match = substr($matches[0], 1, strlen($matches[0]));

    if (!array_key_exists($match, $args2)) {
        return $matches[0];
    }

    return $args2[$match];
}, $uri2);

var_export($uri2);
