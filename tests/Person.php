<?php

namespace MongoDriver\Tests;

/**
 * [\MongoDriver\Models\Model(name = "people")]
 */
class Person
{
    /**
     * [\MongoDriver\Models\Fields\Required]
     * @var string $name
     */
    public $name;
    /**
     * @var int $age
     */
    public $age;
}