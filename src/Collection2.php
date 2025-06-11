<?php

namespace Zone\Wildduck;

use Zone\Wildduck\Util\RequestOptions;
use Override;
use IteratorAggregate;
use Zone\Wildduck\ApiOperations\Request;
use InvalidArgumentException;
use ArrayIterator;
use Zone\Wildduck\Util\Util;

class Collection2 extends WildduckObject implements IteratorAggregate
{
    use Request;

    protected array|null $filters = [];

    protected int|null $_total = null;

    protected int|null $_page = null;

    protected string|null $_previousCursor = null;

    protected string|null $_nextCursor = null;

    protected array $_results = [];

    /**
     * Returns the filters.
     *
     * @return array|null the filters
     */
    public function getFilters(): array|null
    {
        return $this->filters;
    }

    /**
     * Sets the filters, removing paging options.
     *
     * @param array|null $filters the filters
     */
    public function setFilters(array|null $filters): void
    {
        $this->filters = $filters;
    }

	/**
	 * @param array|null $resp
	 * @param array|RequestOptions|null $opts
	 *
	 */
    public function __construct(array|null $resp, array|RequestOptions $opts = null)
    {
        parent::__construct($resp['id'] ?? null, $opts);

        if (!isset($resp['results'])) {
            throw new InvalidArgumentException(
                'array cannot be handled as collection, missing "results" key'
            );
        }

        $this->_total = $resp['total'] ?? null;
        $this->_page = $resp['page'] ?? null;
        $this->_previousCursor = $resp['previousCursor'] ?? null;
        $this->_nextCursor = $resp['nextCursor'] ?? null;
        $this->_results = Util::convertToWildduckObject($resp['results'], $opts);
    }

    #[Override]
    public function count(): int
    {
        return count($this->_results);
    }

    #[Override]
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->_results);
    }

    public function map(?callable $fn): array
    {
        return array_map($fn, $this->_results);
    }

    public static function emptyCollection(): static
    {
        return new static(['results' => []]);
    }

    public function isEmpty(): bool
    {
        return $this->_results === [];
    }

    public function getTotal(): int|null
    {
        return $this->_total;
    }

    #[Override]
    public function toArray(): array
    {
        $arr = [];
        $mapped = [];
        foreach ($this->_results as $i) {
            $mapped[] = $i->toArray();
        }

        if ($this->_total !== null) {
            $arr['results'] = $mapped;
            $paginationInfo = [
                'total' => $this->_total,
                'page' => $this->_page,
                'nextCursor' => $this->_nextCursor,
                'previousCursor' => $this->_previousCursor,
            ];
            return array_merge($paginationInfo, $arr);
        }

        return $mapped;
    }

	#[Override]
    public function __debugInfo(): array
    {
        return $this->_results;
    }
}
