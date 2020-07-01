<?php

namespace Zone\Wildduck;

use Zone\Wildduck\Util\Util;

// TODO: Maybe use Fractal\Pagination and Fractal\Resources
class Collection2 extends WildduckObject implements \Countable, \IteratorAggregate
{

    use ApiOperations\Request;

    /** @var array */
    protected $filters = [];

    protected ?int $_total = null;
    protected ?int $_page = null;
    protected ?string $_previousCursor = null;
    protected ?string $_nextCursor = null;
    protected array $_results = [];

    /**
     * Returns the filters.
     *
     * @return array the filters
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Sets the filters, removing paging options.
     *
     * @param array $filters the filters
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;
    }

    public function __construct($resp, $opts = null)
    {
        if (!isset($resp['results'])) {
            throw new \InvalidArgumentException(
                'array cannot be handled as collection, ' .
                'missing "results" key'
            );
        }

        $this->_total = $resp['total'] ?? null;
        $this->_page = $resp['page'] ?? null;
        $this->_previousCursor = $resp['previousCursor'] ?? null;
        $this->_nextCursor = $resp['nextCursor'] ?? null;
        $this->_results = Util::convertToWildduckObject($resp['results'], $opts);
    }

    public function count()
    {
        return count($this->_results);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->_results);
    }

    public function map($fn)
    {
        $this->_results = array_map($fn, $this->_results);
    }

    public static function emptyCollection()
    {
        return new static(['results' => []]);
    }

    public function isEmpty()
    {
        return empty($this->_results);
    }

    public function nextPage()
    {
        if (null === $this->_nextCursor) {
            return static::emptyCollection();
        }

        $params = [
            'next' => $this->_nextCursor,
            'page' => $this->_page + 1,
        ];

        return $this->all($params);
    }

    public function previousPage()
    {
        if (null === $this->_previousCursor) {
            return static::emptyCollection();
        }

        $params = [
            'previous' => $this->_previousCursor,
            'page' => $this->_page - 1,
        ];

        return $this->all($params);
    }

    public function getTotal()
    {
        return $this->_total;
    }

    public function toArray(): array
    {
        $mapped = [];
        foreach ($this->_results as $i) {
            array_push($mapped, $i->toArray());
        }

        return $mapped;
    }

    public function toJSON(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }

    public function __debugInfo()
    {
        return $this->_results;
    }
}