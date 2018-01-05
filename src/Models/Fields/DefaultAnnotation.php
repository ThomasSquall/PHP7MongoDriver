<?php

namespace MongoDriver\Models\Fields;

use MongoDriver\Models\AnnotationBase;

class DefaultAnnotation extends AnnotationBase
{
    private $value;

    /**
     * DefaultAnnotation constructor.
     * @param string $value
     */
    public function __construct($value = '') { $this->value = $value; }

    protected function getValue() { return $this->value; }
}