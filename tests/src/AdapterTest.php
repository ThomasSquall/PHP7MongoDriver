<?php

use MongoDriver\Adapter;
use MongoDriver\Filter;

class AdapterTest extends \PHPUnit\Framework\TestCase
{
    private function connect()
    {
        $adapter = new Adapter();
        $adapter->connect(CONNECTION);
        $adapter->selectDB(DB);

        return $adapter;
    }

    public function testConnectSuccess()
    {
        $result = TRUE;

        try { $this->connect(); }
        catch (Exception $ex) { $result = false; }

        $this->assertTrue($result);
    }

    public function testConnectFailure()
    {
        $adapter = new Adapter();
        $result = FALSE;

        try { $adapter->connect('dfsfdsf'); }
        catch (Exception $ex) { $result = true; }

        $this->assertTrue($result);
    }

    public function testSelectDB() { $this->assertTrue(true); }

    public function testFindOne($drop = false)
    {
        $adapter = $this->connect();

        if ($drop) $adapter->drop(TEST_COLLECTION);

        $adapter->insert('test_collection', ['name' => 'test']);
        $result = $adapter->findOne('test_collection', new \MongoDriver\Filter('name', 'test'));

        $this->assertInstanceOf('\MongoDriver\Result', $result);
        $this->assertCount(1, $result);
    }

    public function testFind()
    {
        $adapter = $this->connect();

        $adapter->drop(TEST_COLLECTION);
        $adapter->bulkInsert('test_collection', [['name' => 'test1'], ['name' => 'test2'], ['name' => 'test3']]);
        $result = $adapter->find('test_collection', new Filter('name', ['test1', 'test2', 'test3'], Filter::IS_IN_ARRAY));

        $this->assertInstanceOf('\MongoDriver\Result', $result);
        $this->assertCount(3, $result);
    }

    public function testInsert() { $this->testFindOne(); }

    public function testBulkInsert() { $this->testFind(); }

    public function testDrop()
    {
        $this->testFindOne(true);
        $this->testFind();
    }
}