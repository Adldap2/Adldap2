<?php

namespace Adldap\Tests;

class AdldapObjectTest extends FunctionalTestCase
{
    protected function newAbstractObjectMock()
    {
        return $this->mock('Adldap\Objects\AbstractObject');
    }

    public function testObjectSetAttribute()
    {
        $object = $this->newAbstractObjectMock()->makePartial();

        $object->setAttribute('Key', 'Value');

        $expected = [
            'Key' => 'Value',
        ];

        $this->assertEquals($expected, $object->getAttributes());
    }

    public function testObjectSetAttributes()
    {
        $object = $this->newAbstractObjectMock()->makePartial();

        $expected = [
            'Key' => 'Value',
        ];

        $object->setAttributes($expected);

        $this->assertEquals($expected, $object->getAttributes());
    }

    public function testObjectHasAttribute()
    {
        $object = $this->newAbstractObjectMock()->makePartial();

        $attributes = [
            'Key' => 'Value',
        ];

        $object->setAttributes($attributes);

        $this->assertTrue($object->hasAttribute('Key'));
        $this->assertFalse($object->hasAttribute('Does Not Exist'));
    }

    public function testObjectCountAttributes()
    {
        $object = $this->newAbstractObjectMock()->makePartial();

        $attributes = [
            'One',
            'Two',
            'Three',
            'Four'
        ];

        $object->setAttributes($attributes);

        $this->assertEquals(4, $object->countAttributes());
    }

    public function testObjectValidateRequiredPass()
    {
        $object = $this->newAbstractObjectMock()->makePartial();

        $required = [
            'required'
        ];

        $object->setRequired($required);

        $object->setAttribute('required', 'Pass');

        $this->assertTrue($object->validateRequired());
    }

    public function testObjectValidateRequiredFailure()
    {
        $object = $this->newAbstractObjectMock()->makePartial();

        $required = [
            'required'
        ];

        $object->setRequired($required);

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $object->validateRequired();
    }

    public function testObjectValidateRequiredOnlyPass()
    {
        $object = $this->newAbstractObjectMock()->makePartial();

        $required = [
            'required',
            'another',
        ];

        $object->setRequired($required);

        $object->setAttribute('required', 'Pass');

        $object->validateRequired(['required']);
    }

    public function testObjectValidateRequiredOnlyFailure()
    {
        $object = $this->newAbstractObjectMock()->makePartial();

        $required = [
            'required',
            'another',
        ];

        $object->setRequired($required);

        $object->setAttribute('required', 'Pass');

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $object->validateRequired(['another']);
    }
}