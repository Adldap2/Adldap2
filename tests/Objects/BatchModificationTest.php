<?php

namespace Adldap\Tests\Objects;

use Adldap\Objects\BatchModification;
use Adldap\Tests\UnitTestCase;

class BatchModificationTest extends UnitTestCase
{
    public function test_build_with_original()
    {
        $modification = new BatchModification();

        $modification->setOriginal('Test');
        $modification->setAttribute('cn');
        $modification->setValues(['New CN']);

        $modification->build();

        $this->assertEquals(LDAP_MODIFY_BATCH_REPLACE, $modification->getType());
    }

    public function test_build_without_original()
    {
        $modification = new BatchModification();

        $modification->setAttribute('cn');
        $modification->setValues(['New CN']);

        $modification->build();

        $this->assertEquals(LDAP_MODIFY_BATCH_ADD, $modification->getType());
    }

    public function test_build_with_original_and_null_value()
    {
        $modification = new BatchModification();

        $modification->setOriginal('Test');
        $modification->setAttribute('cn');
        $modification->setValues([null]);

        $modification->build();

        $this->assertEquals(LDAP_MODIFY_BATCH_REMOVE_ALL, $modification->getType());
    }

    public function test_build_without_original_and_null_value()
    {
        $modification = new BatchModification();

        $modification->setAttribute('cn');
        $modification->setValues([null]);

        $modification->build();

        $this->assertNull($modification->getType());
    }

    public function test_get()
    {
        $modification = new BatchModification();

        $modification->setValues(['test']);
        $modification->setAttribute('cn');
        $modification->setType(3);

        $expected = [
            'attrib'  => 'cn',
            'modtype' => 3,
            'values'  => ['test'],
        ];

        $this->assertEquals($expected, $modification->get());
    }

    public function test_get_with_invalid_type()
    {
        $modification = new BatchModification();

        $modification->setValues(['test']);
        $modification->setAttribute('cn');
        $modification->setType(100);

        $this->assertNull($modification->get());
    }

    public function test_set_values()
    {
        $modification = new BatchModification();

        $modification->setValues(['test']);

        $this->assertEquals(['test'], $modification->getValues());
    }

    public function test_set_type()
    {
        $modification = new BatchModification();

        $modification->setType(1);

        $this->assertEquals(1, $modification->getType());
    }

    public function test_set_attribute()
    {
        $modification = new BatchModification();

        $modification->setAttribute('test');

        $this->assertEquals('test', $modification->getAttribute());
    }

    public function test_set_original()
    {
        $modification = new BatchModification();

        $modification->setOriginal(['testing']);

        $this->assertEquals(['testing'], $modification->getOriginal());
    }
}
