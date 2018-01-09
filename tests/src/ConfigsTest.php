<?php

use MongoDriver\Adapter;
use MongoDriver\Configs;
use MongoDriver\Tests\Car;
use MongoDriver\Tests\NoModel;
use MongoDriver\Tests\Person;

class ConfigsTest extends \PHPUnit\Framework\TestCase
{
    private function connect()
    {
        $adapter = new Adapter();
        $adapter->connect(CONNECTION_STRING);
        $adapter->selectDB(DB);

        return $adapter;
    }

    public function testRegisterModelSuccess()
    {
        $exception = '';

        try { Configs::registerModel(DB, new Car()); }
        catch (Exception $ex) { $exception = $ex->getMessage(); }

        $this->assertEquals('', $exception);
    }

    public function testRegisterModelFailure()
    {
        $exception = '';
        $model = new NoModel();

        try { Configs::registerModel(DB, $model); }
        catch (Exception $ex) { $exception = $ex->getMessage(); }

        $this->assertEquals("No Model annotation found in class " . get_class($model), $exception);
    }

    public function testIsModelRegisteredTrue()
    {
        Configs::registerModel(DB, new Car());
        $this->assertTrue(Configs::isModelRegistered(DB, CARS_COLLECTION));
    }

    public function testIsModelRegisteredFalse()
    {
        $this->assertFalse(Configs::isModelRegistered(DB, PEOPLE_COLLECTION));
    }

    public function testGetModelExisting()
    {
        $this->assertInstanceOf('\MongoDriver\Tests\Car', Configs::getModel(DB, CARS_COLLECTION));
    }

    public function testGetModelNull()
    {
        $this->assertNull(Configs::getModel(DB, PEOPLE_COLLECTION));
    }
}