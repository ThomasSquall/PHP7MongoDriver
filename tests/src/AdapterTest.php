<?php

use MongoDriver\Adapter;
use MongoDriver\Filter;
use MongoDriver\Tests\Car;
use MongoDriver\Tests\NoModel;

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

    public function testDBNotSelectedException()
    {
        $adapter = new Adapter();
        $adapter->connect(CONNECTION);

        $result = false;

        try { $adapter->findOne(CARS_COLLECTION); }
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

    public function testRegisterModelSuccess()
    {
        $adapter = $this->connect();
        $exception = '';

        try { $adapter->registerModel(new Car()); }
        catch (Exception $ex) { $exception = $ex->getMessage(); }

        $this->assertEquals('', $exception);
    }

    public function testRegisterModelFailure()
    {
        $adapter = $this->connect();
        $exception = '';
        $model = new NoModel();

        try { $adapter->registerModel($model); }
        catch (Exception $ex) { $exception = $ex->getMessage(); }

        $this->assertEquals("No Model annotation found in class " . get_class($model), $exception);
    }

    public function testIsModelRegisteredTrue()
    {
        $adapter = $this->connect();
        $adapter->registerModel(new Car());
        $this->assertTrue($adapter->isModelRegistered(CARS_COLLECTION));
    }

    public function testIsModelRegisteredFalse()
    {
        $adapter = $this->connect();
        $this->assertFalse($adapter->isModelRegistered(PEOPLE_COLLECTION));
    }

    public function testDoNotStore()
    {
        $adapter = $this->connect();
        $adapter->drop(CARS_COLLECTION);
        $adapter->registerModel(new Car());
        $this->assertTrue($adapter->isModelRegistered(CARS_COLLECTION));

        $car = new Car();
        $car->make = 'FIAT';
        $car->owner = 'Thomas';
        $car->doNotStore = 'Do not store me please :(';

        $adapter->insert(CARS_COLLECTION, $car);

        $result = $adapter->findOne(CARS_COLLECTION);

        $this->assertNull($result[0]->doNotStore);
    }

    public function testGetModelExisting()
    {
        $adapter = $this->connect();
        $adapter->registerModel(new Car());
        $this->assertInstanceOf('\MongoDriver\Tests\Car', $adapter->getModel(CARS_COLLECTION));
    }

    public function testGetModelNull()
    {
        $adapter = $this->connect();
        $this->assertNull($adapter->getModel(PEOPLE_COLLECTION));
    }
}