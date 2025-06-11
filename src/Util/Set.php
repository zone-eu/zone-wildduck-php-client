<?php

namespace Zone\Wildduck\Util;

use AllowDynamicProperties;
use Override;
use ArrayIterator;
use IteratorAggregate;

#[AllowDynamicProperties]
class Set implements IteratorAggregate
{
    private array $_elts = [];

    public function __construct(array $members = [])
    {
        foreach ($members as $item) {
            $this->_elts[$item] = true;
        }
    }

    public function includes(string $elt): bool
    {
        return isset($this->_elts[$elt]);
    }

    public function add(string $elt): void
    {
        $this->_elts[$elt] = true;
    }

    public function discard(string $elt): void
    {
        unset($this->_elts[$elt]);
    }

    public function toArray(): array
    {
        return array_keys($this->_elts);
    }

    #[Override]
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->toArray());
    }
}
