<?php

namespace MongoDriver\Models\Fields;

use MongoDriver\Models\AnnotationBase;

class RefAnnotation extends AnnotationBase
{
    private $model = '';
    private $field = '';

    /**
     * RefAnnotation constructor.
     * @param string $model
     * @param string $field
     */
    public function __construct($model, $field)
    {
        $this->model = $model;
        $this->field = $field;
    }

    protected function getModel() { return $this->model; }
    protected function getField() { return $this->field; }
}