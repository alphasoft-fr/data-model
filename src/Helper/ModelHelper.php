<?php

namespace AlphaSoft\DataModel\Helper;

use AlphaSoft\DataModel\AbstractModel;

final class ModelHelper
{
    public static function getReflection(string $modelName): \ReflectionClass
    {
        $reflection = new \ReflectionClass($modelName);
        if (!$reflection->isSubclassOf(AbstractModel::class)) {
            throw new \InvalidArgumentException($modelName . ' must be an instance of ' . AbstractModel::class);
        }
        return $reflection;
    }

    /**
     * @param string $modelName
     * @param array $data
     * @return array<AbstractModel>
     */
    public static function toCollectionObject(string $modelName, array $data): array
    {
        $collection = [];
        $reflectionClass = self::getReflection($modelName);
        foreach ($data as $item) {
            $model = self::toObjectWithReflection($reflectionClass, $item);
            $collection[] = $model;
        }
        return $collection;
    }

    public static function toObject(string $modelName, array $data): AbstractModel
    {
        return self::toObjectWithReflection(self::getReflection($modelName), $data);
    }

    private static function toObjectWithReflection(\ReflectionClass $reflectionClass, array $data): AbstractModel
    {
        /**
         * @var AbstractModel $model
         */
        $model = $reflectionClass->newInstance();
        $model->hydrate($data);
        return $model;
    }
}
