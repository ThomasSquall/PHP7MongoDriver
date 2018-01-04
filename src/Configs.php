<?php

namespace MongoDriver;

use PHPAnnotations\Reflection\TC_Reflector;

final class Configs
{
    private static $models = [];

    /**
     * Registers a class as a model handler.
     * @param string $db
     * @param string $collection
     * @param object $model
     * @throws \Exception
     */
    public static function registerModel($db, $collection, $model)
    {
        $classReflector = (new TC_Reflector($model))->getClass();

        if (!$classReflector->hasAnnotation('\MongoDriver\Models\ModelAnnotation'))
            throw new \Exception("No Model annotation found in class " . get_class($model));

        if (!isset(self::$models[$db])) self::$models[$db] = [];

        self::$models[$db][$collection] = $model;
    }

    /**
     * Checks if a models has been registered for the given db - collection pair.
     * @param string $db
     * @param string $collection
     * @return bool
     */
    public static function isModelRegistered($db, $collection)
    {
        if (!isset(self::$models[$db])) return false;
        if (!isset(self::$models[$db][$collection])) return false;

        return true;
    }

    /**
     * Gets the model registered for the given db - collection pair.
     * @param string $db
     * @param string $collection
     * @return object
     */
    public static function getModel($db, $collection)
    {
        if (!self::isModelRegistered($db, $collection)) return null;

        return self::$models[$db][$collection];
    }
}