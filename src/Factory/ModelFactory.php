<?php

namespace AlphaSoft\DataModel\Factory;

use AlphaSoft\DataModel\Model;
use InvalidArgumentException;
use ReflectionClass;

final class ModelFactory
{
    /**
     * Create and hydrate a model instance from data.
     *
     * @param string $modelName The name of the model class.
     * @param array $data The data to hydrate the model with.
     * @return Model The hydrated model instance.
     * @throws InvalidArgumentException|\ReflectionException If the model class is invalid or doesn't extend Model.
     */
    public static function createModel(string $modelName, array $data): Model
    {
        $reflectionClass = self::getReflection($modelName);
        $model = self::instantiateModel($reflectionClass);
        $model->hydrate($data);
        return $model;
    }

    /**
     * Create a collection of model instances from an array of data.
     *
     * @param string $modelName The name of the model class.
     * @param array $dataCollection The array of data for the collection.
     * @return Model[] The array of hydrated model instances.
     * @throws InvalidArgumentException|\ReflectionException If the model class is invalid or doesn't extend Model.
     */
    public static function createCollection(string $modelName, array $dataCollection): array
    {
        $reflectionClass = self::getReflection($modelName);
        $collection = [];

        foreach ($dataCollection as $data) {
            $model = self::instantiateModel($reflectionClass);
            $model->hydrate($data);
            $collection[] = $model;
        }

        return $collection;
    }

    /**
     * Get the reflection class for a model.
     *
     * @param string $modelName The name of the model class.
     * @return ReflectionClass The reflection class instance.
     * @throws InvalidArgumentException|\ReflectionException If the model class is invalid or doesn't extend Model.
     */
    private static function getReflection(string $modelName): ReflectionClass
    {
        $reflection = new ReflectionClass($modelName);

        if (!$reflection->isSubclassOf(Model::class)) {
            throw new InvalidArgumentException($modelName . ' must be an instance of ' . Model::class);
        }

        return $reflection;
    }

    /**
     * Instantiate a model using reflection.
     *
     * @param ReflectionClass $reflectionClass The reflection class instance.
     * @return Model The instantiated model.
     * @throws \ReflectionException
     */
    private static function instantiateModel(ReflectionClass $reflectionClass): Model
    {
        return $reflectionClass->newInstance();
    }
}
