<?php

namespace Adldap\Tests\Models\Attributes;

use Adldap\Tests\TestCase;
use Adldap\Models\BatchModification;
use Adldap\Models\Attributes\DistinguishedName;

class BatchModificationTest extends TestCase
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

    /** @expectedException \InvalidArgumentException */
    public function test_set_type_invalid()
    {
        $modification = new BatchModification();

        $modification->setType(100);
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

    public function test_constructor()
    {
        $modification = new BatchModification('attribute', 1, ['testing']);

        $this->assertEquals('attribute', $modification->getAttribute());
        $this->assertEquals(1, $modification->getType());
        $this->assertEquals(['testing'], $modification->getValues());
        $this->assertEmpty($modification->getOriginal());
    }

    public function test_values_are_converted_to_strings()
    {
        $modification = new BatchModification('attribute', 1, [
            500,
            10.5,
            (new DistinguishedName('test'))
        ]);

        $this->assertInternalType('string', $modification->getValues()[0]);
        $this->assertInternalType('string', $modification->getValues()[1]);
        $this->assertInternalType('string', $modification->getValues()[2]);
    }

    public function test_is_valid()
    {
        $mod1 = new BatchModification('attribute', 1);
        $mod2 = new BatchModification('attribute', 2);
        $mod3 = new BatchModification('attribute', 3);
        $mod4 = new BatchModification('attribute', 18);

        $this->assertTrue($mod1->isValid());
        $this->assertTrue($mod2->isValid());
        $this->assertTrue($mod3->isValid());
        $this->assertTrue($mod4->isValid());
    }

    public function test_is_not_valid()
    {
        // Empty modification
        $mod1 = new BatchModification();

        // Building a modification which only contains an attribute and empty type & value.
        $mod2 = new BatchModification('attribute');
        $mod2->build();

        $this->assertFalse($mod1->isValid());
        $this->assertFalse($mod2->isValid());
    }
}
