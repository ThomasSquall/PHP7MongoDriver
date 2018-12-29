<?php

namespace MongoDriver\Tests;

/**
 * @MongoDriver\Models\Model(name = "cars")
 */
class Car
{
    /**
     * @var string $make
     *
     * @MongoDriver\Models\Fields\Required
     */
    public $make;

    /**
     * @var string $owner
     *
     * @MongoDriver\Models\Fields\Ref(model = "\MongoDriver\Tests\Person", field = "name")
     */
    public $owner;

    /**
     * @var string $doNotSave
     *
     * @MongoDriver\Models\Fields\DoNotStore
     */
    public $doNotStore;
}