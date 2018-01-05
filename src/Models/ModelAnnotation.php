<?php

namespace MongoDriver\Models;

class ModelAnnotation extends AnnotationBase
{
    private $name;

    /**
     * Model constructor.
     * @param string $name
     */
    public function __construct($name = '') { $this->name = $name; }

    protected function getName() { return $this->name; }
}