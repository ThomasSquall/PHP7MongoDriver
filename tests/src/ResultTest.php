<?php

use MongoDriver\Adapter;
use MongoDriver\Configs;
use MongoDriver\Filter;
use MongoDriver\Result;
use MongoDriver\Tests\Car;
use MongoDriver\Tests\Person;

class ResultTest extends \PHPUnit\Framework\TestCase
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

    public function testConstructorSuccess()
    {
        $result = true;

        try { new Result([], DB, TEST_COLLECTION, $this->connect()); }
        catch(Exception $ex) { $result = false; }

        $this->assertTrue($result);
    }

    public function testConstructorFailure()
    {
        $result = true;

        try { new Result('', DB, TEST_COLLECTION, $this->connect()); }
        catch(Exception $ex) { $result = false; }

        $this->assertFalse($result);
    }

    public function testPopulate()
    {
        $adapter = $this->connect();

        $adapter->drop(CARS_COLLECTION);
        $adapter->drop(PEOPLE_COLLECTION);

        Configs::registerModel(DB, new Car());
        Configs::registerModel(DB, new Person());

        $car1 = new Car();
        $car1->make = 'FIAT';
        $car1->owner = 'Thomas';
        $car2 = new Car();
        $car2->make = 'VOLKSWAGEN';
        $car2->owner = 'Mattia';
        $car3 = new Car();
        $car3->make = 'SUBARU';
        $car3->owner = 'Mattia';

        $adapter->bulkInsert(CARS_COLLECTION, [$car1, $car2, $car3]);

        $person1 = new Person();
        $person1->name = 'Thomas';
        $person1->age = 27;
        $person2 = new Person();
        $person2->name = 'Mattia';
        $person2->age = 29;

        $adapter->bulkInsert(PEOPLE_COLLECTION, [$person1, $person2]);

        $result = $adapter
            ->findOne(CARS_COLLECTION, new Filter('owner', 'Mattia'))
            ->populate('owner');

        $this->assertCount(1, $result);
        $this->assertEquals(29, $result[0]->owner[0]->age);
    }

    public function testOffsetSetOnNull()
    {
        $result = new Result([], DB, TEST_COLLECTION, $this->connect());
        $this->assertCount(0, $result);

        $result[] = ['name', 'Test'];
        $this->assertCount(1, $result);
        $this->assertEquals(['name', 'Test'], $result[0]);
    }

    public function testOffsetSetOnKey()
    {
        $result = new Result([], DB, TEST_COLLECTION, $this->connect());
        $this->assertCount(0, $result);

        $result['test'] = ['name', 'Test'];
        $this->assertCount(1, $result);
        $this->assertEquals(['name', 'Test'], $result['test']);
    }
}