<?php

namespace Adldap\tests\Objects;

use Adldap\Objects\BatchModification;
use Adldap\Tests\UnitTestCase;

class BatchModificationTest extends UnitTestCase
{
    public function testBuildWithOriginal()
    {
        $modification = new BatchModification();

        $modification->setOriginal('Test');
        $modification->setAttribute('cn');
        $modification->setValues(['New CN']);

        $modification->build();

        $this->assertEquals(LDAP_MODIFY_BATCH_REPLACE, $modification->getType());
    }

    public function testBuildWithoutOriginal()
    {
        $modification = new BatchModification();

        $modification->setAttribute('cn');
        $modification->setValues(['New CN']);

        $modification->build();

        $this->assertEquals(LDAP_MODIFY_BATCH_ADD, $modification->getType());
    }

    public function testBuildWithOriginalAndNullValue()
    {
        $modification = new BatchModification();

        $modification->setOriginal('Test');
        $modification->setAttribute('cn');
        $modification->setValues([null]);

        $modification->build();

        $this->assertEquals(LDAP_MODIFY_BATCH_REMOVE_ALL, $modification->getType());
    }

    public function testBuildWithoutOriginalAndNullValue()
    {
        $modification = new BatchModification();

        $modification->setAttribute('cn');
        $modification->setValues([null]);

        $modification->build();

        $this->assertNull($modification->getType());
    }

    public function testGet()
    {
        $modification = new BatchModification();

        $modification->setValues(['test']);
        $modification->setAttribute('cn');
        $modification->setType(3);

        $expected = [
            'attrib' => 'cn',
            'modtype' => 3,
            'values' => ['test'],
        ];

        $this->assertEquals($expected, $modification->get());
    }

    public function testGetWithInvalidType()
    {
        $modification = new BatchModification();

        $modification->setValues(['test']);
        $modification->setAttribute('cn');
        $modification->setType(100);

        $this->assertNull($modification->get());
    }

    public function testSetValues()
    {
        $modification = new BatchModification();

        $modification->setValues(['test']);

        $this->assertEquals(['test'], $modification->getValues());
    }

    public function testSetType()
    {
        $modification = new BatchModification();

        $modification->setType(1);

        $this->assertEquals(1, $modification->getType());
    }

    public function testSetAttribute()
    {
        $modification = new BatchModification();

        $modification->setAttribute('test');

        $this->assertEquals('test', $modification->getAttribute());
    }

    public function testSetOriginal()
    {
        $modification = new BatchModification();

        $modification->setOriginal(['testing']);

        $this->assertEquals(['testing'], $modification->getOriginal());
    }
}
