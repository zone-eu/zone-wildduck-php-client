<?php

namespace Zone\Wildduck\Resource;

use Zone\Wildduck\MailingList;
use Zone\Wildduck\Outbound;
use Zone\Wildduck\Recipient;

/**
 * @property bool $success Indicates successful response (required)
 * @property int $id Message ID (required)
 * @property string $mailbox ID of the Mailbox (required)
 * @property string $user ID of the User (required)
 * @property object $envelope SMTP envelope (if available) (required)
 * @property string $thread ID of the Thread (required)
 * @property Recipient $from Address object (required)
 * @property Recipient $replyTo Address object
 * @property list<Recipient> $to Address object
 * @property list<Recipient> $cc Address object
 * @property list<Recipient> $bcc Address object
 * @property string $subject Message subject (required)
 * @property string $messageId Message-ID header (required)
 * @property string $date Date string from header (required)
 * @property string|null $idate Date string of receive time
 * @property MailingList|null $list If set then this message is from a mailing list
 * @property int $size Message size (required)
 * @property string|null $expires Date string, if set then indicates the time after this message is automatically deleted
 * @property bool $seen Does this message have a \Seen flag (required)
 * @property bool $deleted Does this message have a \Deleted flag (required)
 * @property bool $flagged Does this message have a \Flagged flag (required)
 * @property bool $draft Does this message have a \Draft flag (required)
 * @property list<string> $html An array of HTML strings
 * @property string $text Plaintext content of the message
 * @property list<Attachment> $attachments Attachments for the message
 * @property object|null $verificationResults Security verification info if message was received from MX
 * @property object|null $bimi BIMI logo info
 * @property object $contentType Parsed Content-Type header (required)
 * @property object $metaData Custom metadata object set for this message
 * @property array{mailbox: string, is: number, action: "reply"|"replyAll"|"forward", attachments: bool|string[]} $references References (required)
 * @property list<File> $files List of files added to this message as attachments
 * @property list<Outbound> $outbound Outbound queue entries
 * @property object $forwardTargets Forward targets
 * @property object $reference Referenced message info
 * @property bool $answered \Answered flag value (required)
 * @property bool $forwarded $Forwarded flag value (required)
 * @property bool|null $encrypted True if message is encrypted
 */
class Message extends ApiResource
{
    public const string OBJECT_NAME = 'message';
}
