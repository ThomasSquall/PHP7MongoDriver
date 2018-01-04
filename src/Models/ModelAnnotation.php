<?php

namespace MongoDriver\Models;

use \PHPAnnotations\Annotations\TC_Annotation;

class Model extends TC_Annotation
{
    private $name;

    /**
     * Model constructor.
     * @param string $name
     */
    public function __construct($name = '') { $this->name = $name; }

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

    private function getName() { return $this->name; }
}