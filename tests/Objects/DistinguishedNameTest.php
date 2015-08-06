<?php

namespace Adldap\tests\Objects;

use Adldap\Objects\DistinguishedName;
use Adldap\Tests\UnitTestCase;

class DistinguishedNameTest extends UnitTestCase
{
    public function testConstructBase()
    {
        $base = 'dc=corp,dc=org';

        $dn = new DistinguishedName($base);

        $this->assertEquals($base, $dn->get());
    }

    public function testConstructInvalidBase()
    {
        $base = 'invalid base';

        $dn = new DistinguishedName($base);

        $this->assertEmpty($dn->get());
    }

    public function testConstructWithAnotherDnObject()
    {
        $base = new DistinguishedName();

        $base->addDc('org');
        $base->addDc('corp');

        $dn = new DistinguishedName($base);

        $this->assertEquals($base->get(), $dn->get());
    }

    public function testToString()
    {
        $dn = new DistinguishedName();

        $dn->addDc('corp');

        $this->assertEquals('dc=corp', (string) $dn);
    }

    public function testAddDc()
    {
        $dn = new DistinguishedName();

        $dn->addDc('org');
        $dn->addDc('corp');

        $this->assertEquals('dc=corp,dc=org', $dn->get());
    }

    public function testRemoveDc()
    {
        $dn = new DistinguishedName();

        $dn->addDc('org');
        $dn->addDc('corp');

        $dn->removeDc('org');

        $this->assertEquals('dc=corp', $dn->get());
    }

    public function testAddOu()
    {
        $dn = new DistinguishedName();

        $dn->addOu('User Accounts');
        $dn->addOu('Accounting');

        $this->assertEquals('ou=Accounting,ou=User Accounts', $dn->get());
    }

    public function testRemoveOu()
    {
        $dn = new DistinguishedName();

        $dn->addOu('User Accounts');
        $dn->addOu('Accounting');

        $dn->removeOu('User Accounts');

        $this->assertEquals('ou=Accounting', $dn->get());
    }

    public function testAddCn()
    {
        $dn = new DistinguishedName();

        $dn->addCn('Testing');

        $this->assertEquals('cn=Testing', $dn->get());
    }

    public function testRemoveCn()
    {
        $dn = new DistinguishedName();

        $dn->addCn('Testing');

        $dn->removeCn('Testing');

        $this->assertEmpty($dn->get());
    }

    public function testEscaping()
    {
        $dn = new DistinguishedName();

        $dn->addDc('=,#test;<>+');
        $dn->addOu('=,#test;<>+');
        $dn->addCn('=,#test;<>+');

        $this->assertEquals('cn=\3d\2c\23test\3b\3c\3e\2b,ou=\3d\2c\23test\3b\3c\3e\2b,dc=\3d\2c\23test\3b\3c\3e\2b', $dn->get());
    }
}
