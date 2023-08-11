<?php

namespace Test\AlphaSoft\DataModel;

use AlphaSoft\DataModel\Factory\ModelFactory;
use AlphaSoft\DataModel\Model;
use PHPUnit\Framework\TestCase;
use Test\AlphaSoft\DataModel\TestClass\TestModel;

class ModelFactoryTest extends TestCase
{

    public function testCreateModel()
    {
        $data = [
            'fullName' => 'John Doe',
            'age' => 30,
            'price' => 19.99,
        ];

        $model = ModelFactory::createModel(TestModel::class, $data);

        $this->assertInstanceOf(Model::class, $model);
        $this->assertSame('John Doe', $model->getString('fullName'));
        $this->assertSame(30, $model->getInt('age'));
        $this->assertSame(19.99, $model->getFloat('price'));
    }

    public function testCreateCollection()
    {
        $dataCollection = [
            [
                'fullName' => 'John Doe',
                'age' => 30,
            ],
            [
                'fullName' => 'Jane Smith',
                'age' => 25,
            ],
        ];

        $collection = ModelFactory::createCollection(TestModel::class, $dataCollection);

        $this->assertCount(2, $collection);
        $this->assertInstanceOf(Model::class, $collection[0]);
        $this->assertInstanceOf(Model::class, $collection[1]);
        $this->assertSame('John Doe', $collection[0]->getString('fullName'));
        $this->assertSame(30, $collection[0]->getInt('age'));
        $this->assertSame('Jane Smith', $collection[1]->getString('fullName'));
        $this->assertSame(25, $collection[1]->getInt('age'));
    }

    public function testInvalidModelClass()
    {
        $this->expectException(\ReflectionException::class);
        ModelFactory::createModel('InvalidModelClass', []);
    }
}
