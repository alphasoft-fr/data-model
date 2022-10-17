<?php

namespace Test\AlphaSoft\DataModel;

use PHPUnit\Framework\TestCase;
use Test\AlphaSoft\DataModel\TestClass\MyModel;
use Test\AlphaSoft\DataModel\TestClass\Profile;

class ModelTest extends TestCase {

    public function testData()
    {
        $model = new MyModel([
            'first_name' => 'James',
            'last_name' => 'Bond',
            'date_of_birth' => '1988-12-01',
            'roles' => ['ROLE_ADMIN', 'ROLE_USER'],
            'active' => true,
            'has_signature' => 0,
            'profile' => new Profile(),
            'std_class' => new \stdClass()
        ]);
        $this->assertTrue($model->get('active'));
        $this->assertTrue($model->getBool('active'));
        $this->assertNotTrue($model->getBool('has_signature'));
        $this->assertEquals($model->get('first_name'), 'James');
        $this->assertEquals($model->getString('first_name'), 'James');

        $this->assertEquals($model->get('first_name'), 'James');
        $this->assertEquals($model->getString('date_of_birth'), '1988-12-01');
        $this->assertInstanceOf(\DateTimeInterface::class,$model->getDateTime('date_of_birth', 'Y-m-d'));

        $this->assertEquals($model->getArray('roles'), ['ROLE_ADMIN', 'ROLE_USER']);
        $this->assertNull($model->getOrNull('not_exist'));

        $this->assertInstanceOf(Profile::class,$model->getInstanceOf('profile', Profile::class));

        $this->expectException(\LogicException::class);
        $model->getInstanceOf('std_class', Profile::class);
        $model->getArray('profile');
        $model->getInt('first_name');
    }

    public function testException()
    {
        $model = new MyModel();
        $this->expectException(\InvalidArgumentException::class);
        $model->get('fullName');
    }

    public function testSetter()
    {
        $model = new MyModel();
        $model->set('firstname', 'Alpha');
        $model->set('lastname', 'Soft');
        $this->assertEquals($model->get('firstname'), 'Alpha');
        $this->assertEquals($model->get('lastname'), 'Soft');
    }
}
