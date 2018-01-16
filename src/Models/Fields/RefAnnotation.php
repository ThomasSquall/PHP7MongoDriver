<?php

namespace MongoDriver\Models\Fields;

use PHPAnnotations\Annotations\Annotation;

/**
 * Class RefAnnotation.
 * @package MongoDriver\Models\Fields
 */
class RefAnnotation extends Annotation
{
    protected $model = '';
    protected $field = '';

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
}