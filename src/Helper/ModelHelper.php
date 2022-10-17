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
}
