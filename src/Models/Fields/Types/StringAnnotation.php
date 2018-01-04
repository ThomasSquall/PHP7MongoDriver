<?php

namespace MongoDriver\Models\Attributes\Types;

use PHPAnnotations\Annotations\TC_Annotation;

class StringAnnotation extends TC_Annotation
{
    private $name;

    /**
     * StringAnnotation constructor.
     * @param string $name
     */
    public function __construct($name = '') { $this->name = $name; }

    /**
     * __get magic method used to retrieve the field name.
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

    private function getName() { return $this->name; }
}