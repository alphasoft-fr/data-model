<?php

namespace Test\AlphaSoft\DataModel;

use PHPUnit\Framework\TestCase;
use Test\AlphaSoft\DataModel\TestClass\TestModel;

class ModelTest extends TestCase {

    public function testHydrateAndToArray()
    {
        $data = [
            'fullName' => 'John Doe',
            'age' => 30,
            'price' => 19.99,
            'isActive' => true,
            'tags' => ['tag1', 'tag2'],
        ];

        $model = new TestModel($data);

        $this->assertSame($data, $model->toArray());
    }

    public function testGetString()
    {
        $data = [
            'full_name' => 'John Doe',
            'username' => null,
        ];

        $model = new TestModel($data);

        $this->assertSame('John Doe', $model->getString('fullName'));
        $this->assertSame('Default Name', $model->getString('username', 'Default Name'));
    }

    public function testGetInt()
    {
        $data = [
            'age' => 30,
            'quantity' => null,
        ];

        $model = new TestModel($data);

        $this->assertSame(30, $model->getInt('age'));
        $this->assertSame(0, $model->getInt('quantity', 0));
    }

    public function testGetFloat()
    {
        $data = [
            'price' => 19.99,
            'discount' => null,
        ];

        $model = new TestModel($data);

        $this->assertSame(19.99, $model->getFloat('price'));
        $this->assertSame(0.0, $model->getFloat('discount', 0.0));
    }

    public function testGetBool()
    {
        $data = [
            'isActive' => true,
            'isFeatured' => null,
        ];

        $model = new TestModel($data);

        $this->assertTrue($model->getBool('isActive'));
        $this->assertFalse($model->getBool('isFeatured', false));
    }

    public function testGetArray()
    {
        $data = [
            'tags' => ['tag1', 'tag2'],
            'categories' => null,
        ];

        $model = new TestModel($data);

        $this->assertSame(['tag1', 'tag2'], $model->getArray('tags'));
        $this->assertSame([], $model->getArray('categories', []));
    }


    public function testInvalidProperty()
    {
        $model = new TestModel();

        $this->expectException(\InvalidArgumentException::class);
        $model->get('nonExistentProperty');
    }

    public function testGetDateTime()
    {
        $data = [
            'created_at' => '2023-08-10 10:30:00',
            'updated_at' => null,
        ];

        $model = new TestModel($data);

        $createdAt = $model->getDateTime('createdAt');
        $this->assertInstanceOf(\DateTimeInterface::class, $createdAt);
        $this->assertEquals('2023-08-10 10:30:00', $createdAt->format('Y-m-d H:i:s'));

        $updatedAt = $model->getDateTime('updatedAt', 'Y-m-d');
        $this->assertSame(null, $updatedAt);
    }

    public function testToDb()
    {
        $data = [
            'fullName' => 'John Doe',
            'age' => 30,
            'price' => 19.99,
            'isActive' => true,
            'tags' => ['tag1', 'tag2'],
        ];

        $model = new TestModel($data);

        $expectedDbData = [
            'full_name' => 'John Doe',
            'age' => 30,
            'price' => 19.99,
            'is_active' => true,
            'tags' => ['tag1', 'tag2'],
        ];

        $this->assertSame($expectedDbData, $model->toDb());
    }
}
