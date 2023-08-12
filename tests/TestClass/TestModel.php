<?php

namespace Test\AlphaSoft\DataModel\TestClass;

use AlphaSoft\DataModel\Model;

class TestModel extends Model
{
    protected static function getDefaultAttributes(): array
    {
        return [
            'fullName' => '',
            'age' => null,
            'price' => null,
            'isActive' => false,
            'tags' => [],
        ];
    }

    protected static function getDefaultColumnMapping(): array
    {
        return [
            'fullName' => 'full_name',
            'isActive' => 'is_active',
            'createdAt' => 'created_at',
            'updatedAt' => 'updated_at',
        ];
    }

    public static function getPrimaryKeyColumn(): string
    {
        return 'id';
    }
}
