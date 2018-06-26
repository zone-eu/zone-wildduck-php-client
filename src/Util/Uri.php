<?php

namespace Wildduck\Util;

use Wildduck\Exceptions\UriNotFoundException;

class Uri
{
    private static $uris = [
        'addresses.create' => '/users/:user/addresses',
        'addresses.create.forwarded' => '/addresses/forwarded',
        'addresses.get' => '/users/:user/addresses/:address',
        'addresses.delete' => '/users/:user/addresses/:address',
        'addresses.delete.forwarded' => '/addresses/forwarded/:address',
        'addresses.list' => '/users/:user/addresses',
        'addresses.update' => '/users/:user/addresses/:id',

        'asps.list' => '/users/:user/asps',
        'asps.create' => '/users/:user/asps',
        'asps.delete' => '/users/:user/asps/:asp',

        'authentication.authenticate' => '/authenticate',

        'autoreplies.get' => '/users/:user/autoreply',
        'autoreplies.update' => '/users/:user/autoreply',
        'autoreplies.delete' => '/users/:user/autoreply',

        'filters.create' => '/users/:user/filters',
        'filters.delete' => '/users/:user/filters/:filter',
        'filters.user' => '/users/:user/filters',
        'filters.get' => '/users/:user/filters/:filter',
        'filters.update' => '/users/:user/filters/:filter',

        'two-factor.enable' => '/users/:user/2fa/custom',
        'two-factor.disable' => '/users/:user/2fa/custom',

        'mailboxes.create' => '/users/:user/mailboxes',
        'mailboxes.delete' => '/users/:user/mailboxes/:mailbox',
        'mailboxes.list' => '/users/:user/mailboxes',
        'mailboxes.get' => '/users/:user/mailboxes/:mailbox',
        'mailboxes.update' => '/users/:user/mailboxes/:mailbox',

        'messages.list' => '/users/:user/mailboxes/:mailbox/messages',
        'messages.get' => '/users/:user/mailboxes/:mailbox/messages/:message',

        'users.get' => '/users/:id',
        'users.create' => '/users',
        'users.delete' => '/users/:id',
        'users.resolve' => '/users/resolve/:username',
        'users.update' => '/users/:id',
    ];

    /**
     * @param string $keyword
     * @param array $args
     * @return string
     * @throws UriNotFoundException
     */
    public static function get($keyword, $args = [])
    {
        if (!array_key_exists($keyword, self::$uris)) {
            throw new UriNotFoundException($keyword);
        }

        $uri = self::$uris[$keyword];

        if (strpos($uri, ':') !== false) {
            $uri = preg_replace_callback('/(:[a-z]+)/', function ($matches) use ($args) {
                $match = substr($matches[0], 1, strlen($matches[0]));

                // If the matched placeholder was not found from supplied arguments, keep placeholder intact
                if (!array_key_exists($match, $args)) {
                    return $matches[0];
                }

                return $args[$match];
            }, $uri);
        }

        return $uri;
    }
}
