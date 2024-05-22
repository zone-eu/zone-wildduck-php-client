<?php

namespace Zone\Wildduck\Util;

use Zone\Wildduck\Resource\Address;
use Zone\Wildduck\Resource\ApplicationPassword;
use Zone\Wildduck\Resource\Attachment;
use Zone\Wildduck\Audit;
use Zone\Wildduck\Resource\Autoreply;
use Zone\Wildduck\Resource\Dkim;
use Zone\Wildduck\Resource\DomainAlias;
use Zone\Wildduck\Event;
use Zone\Wildduck\Resource\File;
use Zone\Wildduck\Resource\Filter;
use Zone\Wildduck\Resource\ForwardedAddress;
use Zone\Wildduck\Resource\Mailbox;
use Zone\Wildduck\Resource\Message;
use Zone\Wildduck\Resource\User;
use Zone\Wildduck\Resource\Webhook;

class ObjectTypes
{
    /**
     * @var array Mapping from object types to resource classes
     */
    public const array MAPPING = [
        Address::OBJECT_NAME => Address::class,
        ApplicationPassword::OBJECT_NAME => ApplicationPassword::class,
        Attachment::OBJECT_NAME => Attachment::class,
        Audit::OBJECT_NAME => Audit::class,
        Autoreply::OBJECT_NAME => Autoreply::class,
        Dkim::OBJECT_NAME => Dkim::class,
        DomainAlias::OBJECT_NAME => DomainAlias::class,
        Event::OBJECT_NAME => Event::class,
        File::OBJECT_NAME => Mailbox::class,
        Filter::OBJECT_NAME => Filter::class,
        ForwardedAddress::OBJECT_NAME => ForwardedAddress::class,
        Mailbox::OBJECT_NAME => Mailbox::class,
        Message::OBJECT_NAME => Message::class,
        User::OBJECT_NAME => User::class,
        Webhook::OBJECT_NAME => Webhook::class,
    ];
}
