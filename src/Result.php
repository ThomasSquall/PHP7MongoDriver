<?php

namespace MongoDriver;

use PHPAnnotations\Reflection\Reflector;

class Result implements \IteratorAggregate, \ArrayAccess, \Countable
{
    private $items = [];
    private $db = '';
    private $collection = '';
    private $adapter = null;

    /**
     * Result constructor.
     * @param array $items
     * @param string $db
     * @param string $collection
     * @param \MongoDriver\Adapter $adapter
     * @throws \Exception
     */
    public function __construct($items, $db, $collection, $adapter)
    {
        if (!is_array($items)) throw new \Exception("Given variable for items should be an array");

        $this->items = $items;
        $this->db = $db;
        $this->collection = $collection;
        $this->adapter = $adapter;

        if ($this->adapter->isModelRegistered($this->collection))
        {
            $items = [];
            $class = get_class($this->adapter->getModel($this->collection));

            foreach ($this->items as $fields)
            {
                $item = new $class();

                foreach ($fields as $field => $value)
                {
                    if (property_exists($class, $field) || $field === '_id')
                    {
                        $item->$field = $value;
                    }
                }

                $items[] = $item;
            }

            $this->items = $items;
        }
    }

    /**
     * Populates the field with documents matching the value from the referenced collection.
     * @param string $field
     * @return $this
     */
    public function populate($field)
    {
        if (!$this->adapter->isModelRegistered($this->collection)) return $this;

        $reflector = new Reflector($this->adapter->getModel($this->collection));
        $fieldAnnotations = $reflector->getProperty($field);

        if (is_null($fieldAnnotations)) return $this;
        if (!$fieldAnnotations->hasAnnotation('\MongoDriver\Models\Fields\Ref')) return $this;

        $reference = $fieldAnnotations->getAnnotation('\MongoDriver\Models\Fields\Ref');
        $model = $reference->model;

        if (!class_exists($model)) return $this;

        $referenceReflector = (new Reflector(new $model()))->getClass();

        if (!$referenceReflector->hasAnnotation('\MongoDriver\Models\Model')) return $this;

        $referenceCollection = $referenceReflector->getAnnotation('\MongoDriver\Models\Model');

        if (!$this->adapter->isModelRegistered($referenceCollection->name)) return $this;

        foreach ($this->items as &$item)
        {
            $filter = new Filter($reference->field, $item->$field);
            $item->$field = $this->adapter->find($referenceCollection->name, $filter);
        }

        return $this;
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator() { return new \ArrayIterator($this->items); }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset) { return isset($this->items[$offset]); }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset) { return isset($this->items[$offset]) ? $this->items[$offset] : null; }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) $this->items[] = $value;
        else $this->items[$offset] = $value;
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset) { unset($this->items[$offset]); }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count() { return count($this->items); }
}