<?php

namespace Zone\Wildduck\Util;

use AllowDynamicProperties;
use Zone\Wildduck\Exception\InvalidArgumentException;

#[AllowDynamicProperties]
class RequestOptions
{
    /**
     * @var array<string> a list of headers that should be persisted across requests
     */
    public static array $HEADERS_TO_PERSIST = [];

    public string|null $apiKey = null;

    /** @var null|string */
    public string|null $apiBase;

    /** @var null|object|string */
    public string|object|null $object;

    /** @var bool */
    public bool $raw;
    public array|string $headers;
    public string|null $accessToken;

    /**
     * @param null|string $accessToken
     * @param array<string, string> $headers
     *
     * @param null|string $apiBase
     * @param null|object|string $object
     * @param bool $raw
     */
    public function __construct(string|null $accessToken = null, array|string $headers = [], string|null $apiBase = null, null|object|string $object = null, bool $raw = false)
    {
        $this->accessToken = $accessToken;
        $this->headers = $headers;
        $this->apiBase = $apiBase;
        $this->object = $object;
        $this->raw = $raw;
    }

    /**
     * @return array<string, string>
     */
    public function __debugInfo(): array
    {
        return [
            'accessToken' => $this->redactedApiKey(),
            'headers' => $this->headers,
            'apiBase' => $this->apiBase,
        ];
    }

    /**
     * Unpacks an options array and merges it into the existing RequestOptions
     * object.
     *
     * @param array|null|RequestOptions|string $options a key => value array
     * @param bool $strict when true, forbid string form and arbitrary keys in array form
     */
    public function merge(array|null|RequestOptions|string $options, bool $strict = false): self
    {
        $other_options = self::parse($options, $strict);
        if (null === $other_options->accessToken) {
            $other_options->accessToken = $this->accessToken;
        }

        if (null === $other_options->apiBase) {
            $other_options->apiBase = $this->apiBase;
        }

        $other_options->headers = array_merge($this->headers, $other_options->headers);

        return $other_options;
    }

    /**
     * Discards all headers that we don't want to persist across requests.
     */
    public function discardNonPersistentHeaders(): void
    {
        foreach (array_keys($this->headers) as $k) {
            if (!in_array($k, self::$HEADERS_TO_PERSIST, true)) {
                unset($this->headers[$k]);
            }
        }
    }

    /**
     * Unpacks an options array into an RequestOptions object.
     *
     * @param array|null|RequestOptions|string $options a key => value array
     * @param bool $strict when true, forbid string form and arbitrary keys in array form
     *
     * @throws InvalidArgumentException
     */
    public static function parse(array|null|RequestOptions|string $options, bool $strict = false): self
    {
        if ($options instanceof self) {
            return $options;
        }

        if (null === $options) {
            return new RequestOptions(null, [], null);
        }

        if (is_string($options)) {
            if ($strict) {
                $message = 'Do not pass a string for request options. If you want to set the '
                    . 'API key, pass an array like ["api_key" => <apiKey>] instead.';

                throw new InvalidArgumentException($message);
            }

            return new RequestOptions($options, [], null);
        }
        $headers = [];
        $key = null;
        $base = null;
        $object = null;
        $raw = false;
        if (array_key_exists('api_key', $options)) {
            $key = $options['api_key'];
            unset($options['api_key']);
        }
        if (array_key_exists('api_base', $options)) {
            $base = $options['api_base'];
            unset($options['api_base']);
        }
        if (array_key_exists('object', $options)) {
            $object = $options['object'];
            unset($options['object']);
        }
        if (array_key_exists('raw', $options)) {
            $raw = $options['raw'];
            unset($options['raw']);
        }
        if (array_key_exists('headers', $options)) {
            $headers = $options['headers'];
            unset($options['headers']);
        }
        if ($strict && $options !== []) {
            $message = 'Got unexpected keys in options array: ' . implode(', ', array_keys($options));

            throw new InvalidArgumentException($message);
        }
        return new RequestOptions($key, $headers, $base, $object, $raw);
    }

    private function redactedApiKey(): string
    {
        $pieces = explode('_', (string) $this->accessToken, 3);
        $last = array_pop($pieces);
        $redactedLast = strlen($last) > 4
            ? (str_repeat('*', strlen($last) - 4) . substr($last, -4))
            : $last;
        $pieces[] = $redactedLast;

        return implode('_', $pieces);
    }
}
