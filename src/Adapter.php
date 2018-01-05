<?php

namespace MongoDriver;

include_once "Result.php";

use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;
use MongoDB\Driver\BulkWrite;

class Adapter
{
    /** @var Manager $db */
    private $db;
    /** @var string $dbName */
    private $dbName;

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
     * Finds the items in the collection matching the filters.
     * @param string $collection
     * @param \MongoDriver\Filter[]|\MongoDriver\Filter $filters
     * @param array $options
     * @return Result
     */
    public function find($collection, $filters = [], $options = [])
    {
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

        $result = [];

        foreach ($rows as $row) $result[] = $row;

        return new Result($result, $this->dbName, $collection, $this);
    }

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
     * Inserts an item.
     * @param string $collection
     * @param array|object $item
     */
    public function insert($collection, $item) { $this->bulkInsert($collection, [$item]); }

    /**
     * Inserts an array of items.
     * @param string $collection
     * @param array $items
     */
    public function bulkInsert($collection, $items)
    {
        if (!is_array($items) || count($items) == 0) return;

        $bulk = new BulkWrite(['ordered'=>TRUE]);

        foreach ($items as $item) $bulk->insert($item);

        $this->db->executeBulkWrite("$this->dbName.$collection", $bulk);
    }
}