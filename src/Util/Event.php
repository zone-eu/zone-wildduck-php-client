<?php

namespace Zone\Wildduck\Util;

use AllowDynamicProperties;
use InvalidArgumentException;

#[AllowDynamicProperties]
class Event
{
    public const string END_OF_LINE = "/\r\n|\n|\r/";

    /** @var string */
    private string $data;

    /** @var string */
    private string $eventType;

    /** @var string|null */
    private string|null $id;

    /** @var int|null */
    private int|null $retry;

    /**
     * @param string $data
     * @param string $eventType
     * @param string|null $id
     * @param int|null $retry
     */
    final public function __construct(string $data = '', string $eventType = 'message', string|null $id = null, int|null $retry = null)
    {
        $this->data = $data;
        $this->eventType = $eventType;
        $this->id = $id;
        $this->retry = $retry;
    }

	/**
	 * @param string $raw
	 * @return static
	 */
    public static function parse(string $raw): static
    {
        $event = new static();
        $lines = preg_split(self::END_OF_LINE, $raw);

        foreach ($lines as $line) {
            $matched = preg_match('/(?P<name>[^:]*):?( ?(?P<value>.*))?/', $line, $matches);

            if (!$matched) {
                throw new InvalidArgumentException(sprintf('Invalid line %s', $line));
            }

            $name = $matches['name'];
            $value = $matches['value'];

            if ($name === '') {
                // ignore comments
                continue;
            }

            switch ($name) {
                case 'event':
                    $event->eventType = $value;
                    break;
                case 'data':
                    $event->data = $event->data === '' || $event->data === '0' ? $value : $event->data . PHP_EOL . $value;
                    break;
                case 'id':
                    $event->id = $value;
                    break;
                case 'retry':
                    $event->retry = (int) $value;
                    break;
                default:
                    // The field is ignored.
                    break;
            }
        }

        return $event;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getEventType(): string
    {
        return $this->eventType;
    }

    public function getId(): string|null
    {
        return $this->id;
    }

    public function getRetry(): int|null
    {
        return $this->retry;
    }
}
