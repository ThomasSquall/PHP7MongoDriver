<?php

include_once dirname(__FILE__) . "/../../autoload.php";

use MongoDriver\Adapter;
use MongoDriver\Filter;

class AdapterTest extends \PHPUnit\Framework\TestCase
{
    private $connection='mongodb://localhost:27017';
    private $db='mongo_driver_test';
    private $collection='test_collection';

    private function connect()
    {
        $adapter = new Adapter();
        $adapter->connect($this->connection);
        $adapter->selectDB($this->db);

        return $adapter;
    }

    public function testConnectSuccessful()
    {
        $result = TRUE;

        try { $this->connect(); }
        catch (Exception $ex) { $result = FALSE; }

        $this->assertTrue($result);
    }

    public function testConnectFail()
    {
        $adapter = new Adapter();
        $result = FALSE;

        try { $adapter->connect('dfsfdsf'); }
        catch (Exception $ex) { $result = TRUE; }

        $this->assertTrue($result);
    }

    public function testSelectDB() { $this->assertTrue(TRUE); }

    public function testFindOne()
    {
        $adapter = $this->connect();

//        $adapter->drop($this->collection);
        $adapter->insert('test_collection', ['name' => 'test']);
        $result = $adapter->findOne('test_collection', new \MongoDriver\Filter('name', 'test'));

        $this->assertInstanceOf('\MongoDriver\Result', $result);
        $this->assertCount(1, $result);
    }

    public function testFind()
    {
        $adapter = $this->connect();

        $adapter->drop($this->collection);
        $adapter->bulkInsert('test_collection', [['name' => 'test1'], ['name' => 'test2'], ['name' => 'test3']]);
        $result = $adapter->find('test_collection', new Filter('name', ['test1', 'test2', 'test3'], Filter::IS_IN_ARRAY));

        $this->assertInstanceOf('\MongoDriver\Result', $result);
        $this->assertCount(3, $result);
    }

    public function testInsert() { $this->testFindOne(); }

    public function testBulkInsert() { $this->testFind(); }

    public function testDrop()
    {
        $this->testFind();
        $this->testFindOne();
    }
}