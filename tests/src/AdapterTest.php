<?php

use MongoDriver\Adapter;
use MongoDriver\Filter;

class AdapterTest extends \PHPUnit\Framework\TestCase
{
    private $adapter = null;

    private function connect()
    {
        if (is_null($this->adapter))
        {
            $this->adapter = new Adapter();
            $this->adapter->connect(CONNECTION);
            $this->adapter->selectDB(DB);
        }

        return $this->adapter;
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

    public function testFindOne()
    {
        $adapter = $this->connect();

        $adapter->drop(TEST_COLLECTION);
        $adapter->insert(TEST_COLLECTION, ['name' => 'test']);
        $result = $adapter->findOne(TEST_COLLECTION, new \MongoDriver\Filter('name', 'test'));

        $this->assertInstanceOf('\MongoDriver\Result', $result);
        $this->assertCount(1, $result);
    }

    public function testFind()
    {
        $adapter = $this->connect();

        $adapter->drop(TEST_COLLECTION);
        $adapter->bulkInsert(TEST_COLLECTION, [['name' => 'test1'], ['name' => 'test2'], ['name' => 'test3']]);
        $result = $adapter->find(TEST_COLLECTION, new Filter('name', ['test1', 'test2', 'test3'], Filter::IS_IN_ARRAY));

        $this->assertInstanceOf('\MongoDriver\Result', $result);
        $this->assertCount(3, $result);
    }

    public function testInsert() { $this->testFindOne(); }

    public function testBulkInsert() { $this->testFind(); }

    public function testListCollections()
    {
        $adapter = $this->connect();

        $this->assertGreaterThanOrEqual(1, count($adapter->listCollections()));
    }

    public function testDrop()
    {
        $this->testFindOne();
        $this->testFind();
    }
}