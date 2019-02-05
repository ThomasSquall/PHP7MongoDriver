<?php

namespace MongoDriver;

use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Command;
use MongoDB\Driver\Cursor;
use PHPAnnotations\Reflection\Reflector;

class Adapter
{
    /** @var Manager $db */
    private $db;
    /** @var string $dbName */
    private $dbName = '';
    /** @var array $models */
    private $models = [];

    /**
     * Registers a class as a model handler.
     * @param object $model
     * @throws \Exception
     */
    public function registerModel($model)
    {
        $this->checkDB();

        $classReflector = (new Reflector($model))->getClass();

        if (!$classReflector->hasAnnotation('\MongoDriver\Models\Model'))
            throw new \Exception("No Model annotation found in class " . get_class($model));

        $collection = $classReflector->getAnnotation('\MongoDriver\Models\Model')->name;

        if (!isset($this->models[$this->dbName])) $this->models[$this->dbName] = [];

        $this->models[$this->dbName][$collection] = $model;
    }

    /**
     * Checks if a models has been registered for the given db - collection pair.
     * @param string $collection
     * @return bool
     */
    public function isModelRegistered($collection)
    {
        $this->checkDB();

        if (!isset($this->models[$this->dbName])) return false;
        if (!isset($this->models[$this->dbName][$collection])) return false;

        return true;
    }

    /**
     * Gets the model registered for the given db - collection pair.
     * @param string $collection
     * @return object
     */
    public function getModel($collection)
    {
        if (!$this->isModelRegistered($collection)) return null;

        return $this->models[$this->dbName][$collection];
    }

    /**
     * Connects to a mongo database.
     * @param string $connectionString
     * @param string $dbName
     */
    public function connect($connectionString, $dbName = '')
    {
        $this->db = new Manager($connectionString);

        if ($dbName !== '') $this->selectDB($dbName);
    }

    /**
     * Selects the database.
     * @param $dbName
     */
    public function selectDB($dbName) { $this->dbName = $dbName; }

    /**
     * Finds the first item in the collection matching the filters.
     * @param string $collection
     * @param \MongoDriver\Filter[]|\MongoDriver\Filter $filters
     * @param array $options
     * @return Result
     */
    public function findOne($collection, $filters = [], $options = [])
    {
        $options['limit'] = 1;

        return $this->find($collection, $filters, $options);
    }

    /**
     * Finds the items in the collection matching the filters.
     * @param string $collection
     * @param \MongoDriver\Filter[]|\MongoDriver\Filter $filters
     * @param array $options
     * @return Result
     */
    public function find($collection, $filters = [], $options = [])
    {
        $this->checkDB();

        if (is_a($filters, '\MongoDriver\Filter')) $filters = [$filters];

        $filtersArray = [];

        foreach ($filters as $filter)
        {
            $filter = $filter->getFilter();
            reset($filter);
            $key = key($filter);
            $filtersArray[$key] = $filter[$key];
        }

        $query = new Query($filtersArray, $options);
        $rows = $this->db->executeQuery("$this->dbName.$collection", $query);

        return $this->getResult($rows, $collection);
    }

    /**
     * Inserts an item.
     * @param string $collection
     * @param array|object $item
     * @return mixed
     */
    public function insert($collection, $item) { return $this->bulkInsert($collection, [$item])[0]; }

    /**
     * Inserts an array of items.
     * @param string $collection
     * @param array $items
     * @return array
     */
    public function bulkInsert($collection, $items)
    {
        $this->checkDB();

        if (!is_array($items) || count($items) == 0) return [];

        $bulk = new BulkWrite(['ordered'=>TRUE]);

        $result = [];

        foreach ($items as &$item)
        {
            if (is_object($item))
            {
                $reflector = new Reflector($item);

                if ($reflector->getClass()->hasAnnotation('\MongoDriver\Models\Model'))
                {
                    $newItem = [];

                    foreach ($item as $param => $value)
                    {
                        if (!$reflector->getProperty($param)->hasAnnotation('\MongoDriver\Models\Fields\DoNotStore'))
                        {
                            $newItem[$param] = $value;
                        }
                    }

                    $item = $newItem;
                }
            }

            $result[] = $bulk->insert($item);
        }

        $this->db->executeBulkWrite("$this->dbName.$collection", $bulk);

        return $result;
    }

    /**
     * Updates an item based on search fields.
     * @param string $collection
     * @param array|object $search
     * @param array $update
     */
    public function update($collection, $search, $update)
    {
        $this->checkDB();

        $search = $this->objToCleanArray($search);
        $update = $this->objToCleanArray($update);

        $bulk = new BulkWrite();

        $bulk->update
        (
            $search,
            ['$set' => $update],
            ['multi' => false, 'upsert' => false]
        );

        $this->db->executeBulkWrite("$this->dbName.$collection", $bulk);
    }

    /**
     * Deletes an item from the collection bases on the $search fields.
     * @param $collection
     * @param $search
     */
    public function delete($collection, $search)
    {
        $this->checkDB();

        $search = $this->objToCleanArray($search);

        $bulk = new BulkWrite();

        $bulk->delete($search);
        $this->db->executeBulkWrite("$this->dbName.$collection", $bulk);
    }

    /**
     * Lists all the collections.
     * @return Result
     */
    public function listCollections()
    {
        $this->checkDB();

        $command = new Command(["listCollections" => 1]);
        $rows = $this->db->executeCommand($this->dbName, $command);

        return $this->getResult($rows);
    }

    /**
     * Drops a collection if exists.
     * @param string $collection
     */
    public function drop($collection)
    {
        $this->checkDB();

        $collections = $this->listCollections();

        foreach ($collections as $col)
        {
            if ($col->name === $collection)
            {
                $command = new Command(["drop" => $collection]);
                $this->db->executeCommand($this->dbName, $command);

                break;
            }
        }
    }

    private function getResult(Cursor $rows, $collection = '')
    {
        $result = [];

        foreach ($rows as $row) $result[] = $row;

        return new Result($result, $this->dbName, $collection, $this);
    }

    private function checkDB() { if ($this->dbName === '') throw new \Exception("No database has been selected!"); }

    /**
     * @param mixed $obj
     * @return mixed
     */
    private function objToCleanArray($obj)
    {
        if (is_object($obj))
        {
            $tmp = (array)$obj;
            $obj = [];

            foreach ($tmp as $param => $value)
            {
                if (isset($value))
                    $obj[$param] = $value;
            }

            unset($tmp);
        }

        return $obj;
    }
}