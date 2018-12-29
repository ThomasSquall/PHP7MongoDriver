<?php

namespace MongoDriver\Tests;

/**
 * @MongoDriver\Models\Model(name = "people")
 */
class Person
{
    /**
     * @var string $name
     *
     * @MongoDriver\Models\Fields\Required
     */
    public $name;

    /**
     * @var int $age
     */
    public $age;
}