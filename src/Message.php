<?php

namespace Zone\Wildduck;

/**
 * @property int $id
 * @property string $mailbox
 * @property string $user
 * @property object $envelope
 * @property string $thread
 * @property Recipient $from
 * @property Recipient[] $to
 * @property Recipient[] $cc
 * @property Recipient[] $bcc
 * @property string $subject
 * @property string $messageId
 * @property string $date
 * @property MailingList $list
 * @property string $expires
 * @property bool $seen
 * @property bool $deleted
 * @property bool $flagged
 * @property bool $draft
 * @property string[] $html
 * @property string $text
 * @property Attachment[] $attachments
 * @property object $verificationResults
 * @property object $contentType
 * @property object $metaData
 * @property object $reference
 * @property File[] $files
 */
class Message extends ApiResource
{

    const OBJECT_NAME = 'message';
}
