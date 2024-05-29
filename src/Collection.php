<?php

namespace Zone\Wildduck;

use Zone\Wildduck\Exception\ApiConnectionException;
use Zone\Wildduck\Exception\AuthenticationFailedException;
use Zone\Wildduck\Exception\InvalidAccessTokenException;
use Zone\Wildduck\Exception\InvalidDatabaseException;
use Zone\Wildduck\Exception\RequestFailedException;
use Zone\Wildduck\Exception\ValidationException;
use Zone\Wildduck\Util\RequestOptions;
use Override;
use IteratorAggregate;
use Zone\Wildduck\ApiOperations\Request;
use Zone\Wildduck\Exception\InvalidArgumentException;
use Zone\Wildduck\Util\Util;
use Zone\Wildduck\Exception\UnexpectedValueException;
use ArrayIterator;
use Traversable;
use Generator;

/**
 * Class Collection.
 *
 * @property string $object
 * @property string $url
 * @property bool $has_more
 * @property WildduckObject[] $data
 */
class Collection extends WildduckObject implements IteratorAggregate
{
    use Request;

    public const string OBJECT_NAME = 'list';

    protected array $filters = [];

    /**
     * @return string the base URL for the given class
     */
    public static function baseUrl(): string
    {
        return Wildduck::$apiBase;
    }

    /**
     * Returns the filters.
     *
     * @return array the filters
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Sets the filters, removing paging options.
     *
     * @param array $filters the filters
     */
    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }

    /**
     * @param mixed $offset
     * @return string|null
     */
    #[Override]
    public function offsetGet(mixed $offset): string|null
    {
        if (is_string($offset)) {
            return parent::offsetGet($offset);
        }

        $msg = sprintf('You tried to access the %s index, but Collection ', $offset) .
                   'types only support string keys. (HINT: List calls ' .
                   'return an object with a `data` (which is the data ' .
                   sprintf('array). You likely want to call ->data[%s])', $offset);

        throw new InvalidArgumentException($msg);
    }

	/**
	 * @param array|null $params
	 * @param array|null $opts
	 * @return WildduckObject|array
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws InvalidDatabaseException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function all(array|null $params = null, array|null $opts = null): WildduckObject|array
    {
        self::_validateParams($params);
        [$url, $params] = $this->extractPathAndUpdateParams($params);

        [
            $response,
            $opts
        ] = $this->_request('get', $url, $params, $opts);
        $obj = Util::convertToWildduckObject($response, $opts);
        if (!($obj instanceof self)) {
            throw new UnexpectedValueException(
                'Expected type ' . self::class . ', got "' . $obj::class . '" instead.'
            );
        }

        $obj->setFilters($params);

        return $obj;
    }

	/**
	 * @param array|null $params
	 * @param array|null $opts
	 * @return WildduckObject|Collection2|array
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws InvalidDatabaseException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function create(array|null $params = null, array|null $opts = null): WildduckObject|Collection2|array
    {
        self::_validateParams($params);
        [$url, $params] = $this->extractPathAndUpdateParams($params);

        [$response, $opts] = $this->_request('post', $url, $params, $opts);

        return Util::convertToWildduckObject($response, $opts);
    }

	/**
	 * @param array|string|int $id
	 * @param array|null $params
	 * @param array|RequestOptions|null $opts
	 * @return array|WildduckObject
	 * @throws ApiConnectionException
	 * @throws AuthenticationFailedException
	 * @throws InvalidAccessTokenException
	 * @throws InvalidDatabaseException
	 * @throws RequestFailedException
	 * @throws ValidationException
	 */
    public function retrieve(array|string|int $id, array|null $params = null, array|RequestOptions|null $opts = null): array|WildduckObject
    {
        self::_validateParams($params);
        [$url, $params] = $this->extractPathAndUpdateParams($params);

        $id = Util::utf8($id);
        $extn = urlencode((string) $id);
        [$response, $opts] = $this->_request(
            'get',
            sprintf('%s/%s', $url, $extn),
            $params,
            $opts
        );

        return Util::convertToWildduckObject($response, $opts);
    }

    /**
     * @return int the number of objects in the current page
     */
    #[Override]
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * @return ArrayIterator an iterator that can be used to iterate
     *    across objects in the current page
     */
    #[Override]
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }

    /**
     * @return ArrayIterator an iterator that can be used to iterate
     *    backwards across objects in the current page
     */
    public function getReverseIterator(): Traversable
    {
        return new ArrayIterator(array_reverse($this->data));
    }

    /**
     * @return Generator A generator that can be used to
     *    iterate across all objects across all pages. As page boundaries are
     *    encountered, the next page will be fetched automatically for
     *    continued iteration.
     */
    public function autoPagingIterator()
    {
        $page = $this;

        while (true) {
            $filters = $this->filters ?: [];
            if (
                array_key_exists('ending_before', $filters) &&
                !array_key_exists('starting_after', $filters)
            ) {
                foreach ($page->getReverseIterator() as $item) {
                    yield $item;
                }

                $page = $page->previousPage();
            } else {
                foreach ($page as $item) {
                    yield $item;
                }

                $page = $page->nextPage();
            }

            if ($page->isEmpty()) {
                break;
            }
        }
    }

    /**
     * Returns an empty collection. This is returned from {@see nextPage()}
     * when we know that there isn't a next page in order to replicate the
     * behavior of the API when it attempts to return a page beyond the last.
     *
     * @param array|null|RequestOptions|string $opts
     *
     * @return WildduckObject
     */
    public static function emptyCollection(array|null|RequestOptions|string $opts = null): WildduckObject
    {
        return self::constructFrom(['data' => []], $opts);
    }

    /**
     * Returns true if the page object contains no element.
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
    }

    /**
     * Fetches the next page in the resource list (if there is one).
     *
     * This method will try to respect the limit of the current page. If none
     * was given, the default limit will be fetched again.
     *
     *
     * @param array|null $params
     * @param array|string|null $opts
     * @return WildduckObject|Collection
     */
    public function nextPage(array|null $params = null, array|null|string $opts = null): WildduckObject|Collection
    {
        if (!$this->has_more) {
            return static::emptyCollection($opts);
        }

        $lastId = end($this->data)->id;

        $params = array_merge(
            $this->filters ?: [],
            ['starting_after' => $lastId],
            $params ?: []
        );

        return $this->all($params, $opts);
    }

    /**
     * Fetches the previous page in the resource list (if there is one).
     *
     * This method will try to respect the limit of the current page. If none
     * was given, the default limit will be fetched again.
     *
     * @param array|null $params
     * @param array|null|string $opts
     *
     * @return WildduckObject|Collection
     */
    public function previousPage(array|null $params = null, array|null|string $opts = null): WildduckObject|Collection
    {
        if (!$this->has_more) {
            return static::emptyCollection($opts);
        }

        $firstId = $this->data[0]->id;

        $params = array_merge(
            $this->filters ?: [],
            ['ending_before' => $firstId],
            $params ?: []
        );

        return $this->all($params, $opts);
    }

    /**
     * @param array|null $params
     * @return array
     */
    private function extractPathAndUpdateParams(array|null $params): array
    {
        $url = parse_url($this->url);
        if (!isset($url['path'])) {
            throw new UnexpectedValueException('Could not parse list url into parts: ' . $url);
        }

        if (isset($url['query'])) {
            // If the URL contains a query param, parse it out into $params, so they
            // don't interact weirdly with each other.
            $query = [];
            parse_str($url['query'], $query);
            $params = array_merge($params ?: [], $query);
        }

        return [$url['path'], $params];
    }
}
