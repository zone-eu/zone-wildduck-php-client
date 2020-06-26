<?php

namespace Zone\Wildduck\Util;

class ObjectTypes
{
    /**
     * @var array Mapping from object types to resource classes
     */
    const mapping = [
        \Zone\Wildduck\Address::OBJECT_NAME => \Zone\Wildduck\Address::class,
        \Zone\Wildduck\ApplicationPassword::OBJECT_NAME => \Zone\Wildduck\ApplicationPassword::class,
        \Zone\Wildduck\Attachment::OBJECT_NAME => \Zone\Wildduck\Attachment::class,
        \Zone\Wildduck\Audit::OBJECT_NAME => \Zone\Wildduck\Audit::class,
        \Zone\Wildduck\Autoreply::OBJECT_NAME => \Zone\Wildduck\Autoreply::class,
        \Zone\Wildduck\Dkim::OBJECT_NAME => \Zone\Wildduck\Dkim::class,
        \Zone\Wildduck\DomainAlias::OBJECT_NAME => \Zone\Wildduck\DomainAlias::class,
        \Zone\Wildduck\Event::OBJECT_NAME => \Zone\Wildduck\Event::class,
        \Zone\Wildduck\File::OBJECT_NAME => \Zone\Wildduck\Mailbox::class,
        \Zone\Wildduck\Filter::OBJECT_NAME => \Zone\Wildduck\Filter::class,
        \Zone\Wildduck\ForwardedAddress::OBJECT_NAME => \Zone\Wildduck\ForwardedAddress::class,
        \Zone\Wildduck\Mailbox::OBJECT_NAME => \Zone\Wildduck\Mailbox::class,
        \Zone\Wildduck\Message::OBJECT_NAME => \Zone\Wildduck\Message::class,
        \Zone\Wildduck\User::OBJECT_NAME => \Zone\Wildduck\User::class,
    ];
}
