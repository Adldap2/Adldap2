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
}
