<?php

namespace MongoDriver\Models;

use PHPAnnotations\Annotations\Annotation;

/**
 * Class ModelAnnotation.
 * @package MongoDriver\Models
 */
class ModelAnnotation extends Annotation
{
    protected $name;

    /**
     * Model constructor.
     * @param string $name
     */
    public function __construct($name = '') { $this->name = $name; }
}