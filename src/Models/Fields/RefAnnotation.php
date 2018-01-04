<?php

namespace MongoDriver\Models\Fields;

use PHPAnnotations\Annotations\TC_Annotation;

class RefAnnotation extends TC_Annotation
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

    /**
     * __get magic method used to retrieve the name.
     * @param $param
     * @return null
     */
    public function __get($param)
    {
        $result = null;
        $method = 'get' . ucfirst($param);

        if (method_exists($this, $method)) $result = $this->$method();

        return $result;
    }

    private function getModel() { return $this->model; }
    private function getField() { return $this->field; }
}