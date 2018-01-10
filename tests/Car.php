<?php

namespace MongoDriver\Tests;

/**
 * [\MongoDriver\Models\Model(name = "cars")]
 */
class Car
{
    /**
     * [\MongoDriver\Models\Fields\Required]
     * @var string $make
     */
    public $make;

    /**
     * [\MongoDriver\Models\Fields\Ref(model = "\MongoDriver\Tests\Person", field = "name")]
     * @var string $owner
     */
    public $owner;
}