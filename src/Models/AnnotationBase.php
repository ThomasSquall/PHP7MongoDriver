<?php

namespace MongoDriver\Models;

use PHPAnnotations\Annotations\Annotation;

class AnnotationBase extends Annotation
{
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
}