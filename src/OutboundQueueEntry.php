<?php

namespace Zone\Wildduck;

/**
 * @property string $seq Queue target ID
 * @property string $recipient TargetRecipient
 * @property string $sendingZone Zone ID in ZoneMTA
 * @property string $queued ISO Date of the expected delivery time
 */
class OutboundQueueEntry extends WildduckObject
{
}
