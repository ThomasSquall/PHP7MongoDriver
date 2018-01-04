<?php

namespace MongoDriver;

use PHPAnnotations\Annotations\TC_Annotation;
use PHPAnnotations\Reflection\TC_Reflector;

class Result implements \IteratorAggregate
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

        if (Configs::isModelRegistered($this->db, $this->collection))
        {
            $items = [];
            $class = get_class(Configs::getModel($this->db, $this->collection));

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
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator() { return new \ArrayIterator($this->items); }

    public function populate($field)
    {
        if (!Configs::isModelRegistered($this->db, $this->collection)) return $this;

        $reflector = new TC_Reflector(Configs::getModel($this->db, $this->collection));
        $fieldAnnotations = $reflector->getProperty($field);

        if (is_null($fieldAnnotations)) return $this;
        if (!$fieldAnnotations->hasAnnotation('\MongoDriver\Models\Fields\Ref')) return $this;

        $reference = $fieldAnnotations->getAnnotation('\MongoDriver\Models\Fields\Ref');
        $model = $reference->model;

        if (!class_exists($model)) return $this;

        $referenceReflector = (new TC_Reflector(new $model()))->getClass();

        if (!$referenceReflector->hasAnnotation('\MongoDriver\Models\Model')) return $this;

        $referenceCollection = $referenceReflector->getAnnotation('\MongoDriver\Models\Model');

        if (!Configs::isModelRegistered($this->db, $referenceCollection->name)) return $this;

        foreach ($this->items as &$item)
        {
            $filter = new Filter($reference->field, $item->$field);
            $item->$field = $this->adapter->find($referenceCollection->name, $filter);
        }

        return $this;
    }
}