<?php

namespace MongoDriver;

class Result implements \IteratorAggregate
{
    private $items = [];

    /**
     * Result constructor.
     * @param array $items
     * @throws \Exception
     */
    public function __construct($items)
    {
        if (!is_array($items))  throw new \Exception("Given variable for items should be an array");

        $this->items = $items;
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator() { return new \ArrayIterator($this->items); }
}