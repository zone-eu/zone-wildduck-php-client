<?php

namespace Zone\Wildduck\Resource;

use Zone\Wildduck\MailingList;
use Zone\Wildduck\Outbound;
use Zone\Wildduck\Recipient;

/**
 * @property int $id
 * @property bool $success
 * @property string $mailbox
 * @property string $user
 * @property object $envelope
 * @property string $thread
 * @property Recipient $from
 * @property list<Recipient> $to
 * @property list<Recipient> $cc
 * @property list<Recipient> $bcc
 * @property string $subject
 * @property string $messageId
 * @property string $date
 * @property MailingList $list
 * @property string $expires
 * @property bool $seen
 * @property bool $deleted
 * @property bool $flagged
 * @property bool $draft
 * @property list<string> $html
 * @property string $text
 * @property list<Attachment> $attachments
 * @property object $verificationResults
 * @property object $contentType
 * @property object $metaData
 * @property object $reference
 * @property list<File> $files
 * @property list<Outbound> $outbound
 */
class Message extends ApiResource
{
    public const string OBJECT_NAME = 'message';
}
