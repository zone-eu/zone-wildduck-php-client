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
        'authentication.listEvents' => '/users/:user/authlog',
        'authentication.getEvent' => '/users/:user/authlog/:event',

        'autoreplies.get' => '/users/:user/autoreply',
        'autoreplies.update' => '/users/:user/autoreply',
        'autoreplies.delete' => '/users/:user/autoreply',

        'filters.create' => '/users/:user/filters',
        'filters.delete' => '/users/:user/filters/:filter',
        'filters.user' => '/users/:user/filters',
        'filters.get' => '/users/:user/filters/:filter',
        'filters.update' => '/users/:user/filters/:filter',

        'mailboxes.create' => '/users/:user/mailboxes',
        'mailboxes.delete' => '/users/:user/mailboxes/:mailbox',
        'mailboxes.list' => '/users/:user/mailboxes',
        'mailboxes.get' => '/users/:user/mailboxes/:mailbox',
        'mailboxes.update' => '/users/:user/mailboxes/:mailbox',

        'messages.list' => '/users/:user/mailboxes/:mailbox/messages',
        'messages.empty' => '/users/:user/mailboxes/:mailbox/messages',
        'messages.update' => '/users/:user/mailboxes/:mailbox/messages',
        'messages.get' => '/users/:user/mailboxes/:mailbox/messages/:message',
        'messages.delete' => '/users/:user/mailboxes/:mailbox/messages/:message',
        'messages.downloadAttachment' => '/users/:user/mailboxes/:mailbox/messages/:message/attachments/:attachment',
        'messages.events' => '/users/:user/mailboxes/:mailbox/messages/:message/events',
        'messages.source' => '/users/:user/mailboxes/:mailbox/messages/:message/message.eml',
        'messages.search' => '/users/:user/search',
        'messages.submit' => '/users/:user/mailboxes/:mailbox/messages/:message/submit',
        'messages.upload' => '/users/:user/mailboxes/:mailbox/messages',

        'submission.submit' => '/users/:user/submit',

        'two-factor.enable.custom' => '/users/:user/2fa/custom',
        'two-factor.enable.totp' => '/users/:user/2fa/totp/enable',
        'two-factor.disable' => '/users/:user/2fa',
        'two-factor.disable.custom' => '/users/:user/2fa/custom',
        'two-factor.disable.totp' => '/users/:user/2fa/totp',
        'two-factor.generate.totp' => '/users/:user/2fa/totp/setup',
        'two-factor.validate.totp' => '/users/:user/2fa/totp/check',
        'two-factor.generate.u2f' => '/users/:user/2fa/u2f/setup',
        'two-factor.enable.u2f' => '/users/:user/2fa/u2f/enable',
        'two-factor.disable.u2f' => '/users/:user/2fa/u2f',
        'two-factor.start.u2f' => '/users/:user/2fa/u2f/start',
        'two-factor.validate.u2f' => '/users/:user/2fa/u2f/check',

        'users.get' => '/users/:id',
        'users.create' => '/users',
        'users.delete' => '/users/:id',
        'users.resolve' => '/users/resolve/:username',
        'users.update' => '/users/:id',
        'users.stream' => '/users/:id/updates',
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
