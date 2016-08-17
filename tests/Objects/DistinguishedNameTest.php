<?php

namespace Adldap\Tests\Objects;

use Adldap\Tests\TestCase;
use Adldap\Objects\DistinguishedName;

class DistinguishedNameTest extends TestCase
{
    public function test_construct_base()
    {
        $base = 'dc=corp,dc=org';

        $dn = new DistinguishedName($base);

        $this->assertEquals($base, $dn->get());
    }

    public function test_construct_invalid_base()
    {
        $base = 'invalid base';

        $dn = new DistinguishedName($base);

        $this->assertEmpty($dn->get());
    }

    public function test_construct_with_another_dn_object()
    {
        $base = new DistinguishedName();

        $base->addDc('org');
        $base->addDc('corp');

        $dn = new DistinguishedName($base);

        $this->assertEquals($base->get(), $dn->get());
    }

    public function test_to_string()
    {
        $dn = new DistinguishedName();

        $dn->addDc('corp');

        $this->assertEquals('dc=corp', (string) $dn);
    }

    public function test_add_dc()
    {
        $dn = new DistinguishedName();

        $dn
            ->addDc('corp')
            ->addDc('testing')
            ->addDc('org');

        $this->assertEquals('dc=corp,dc=testing,dc=org', $dn->get());
    }

    public function test_remove_dc()
    {
        $dn = new DistinguishedName();

        $dn->addDc('org');
        $dn->addDc('corp');

        $dn->removeDc('org');

        $this->assertEquals('dc=corp', $dn->get());
    }

    public function test_add_ou()
    {
        $dn = new DistinguishedName();

        $dn
            ->addOu('User Accounts')
            ->addOu('Accounting');

        $this->assertEquals('ou=User Accounts,ou=Accounting', $dn->get());
    }

    public function test_remove_ou()
    {
        $dn = new DistinguishedName();

        $dn->addOu('User Accounts');
        $dn->addOu('Accounting');

        $dn->removeOu('User Accounts');

        $this->assertEquals('ou=Accounting', $dn->get());
    }

    public function test_add_cn()
    {
        $dn = new DistinguishedName();

        $dn->addCn('Testing');

        $this->assertEquals('cn=Testing', $dn->get());
    }

    public function test_remove_cn()
    {
        $dn = new DistinguishedName();

        $dn->addCn('Testing');

        $dn->removeCn('Testing');

        $this->assertEmpty($dn->get());
    }

    public function test_escaping()
    {
        $dn = new DistinguishedName();

        $dn->addO('=,#test;<>+');
        $dn->addDc('=,#test;<>+');
        $dn->addOu('=,#test;<>+');
        $dn->addCn('=,#test;<>+');

        $this->assertEquals(
            'cn=\3d\2c\23test\3b\3c\3e\2b,ou=\3d\2c\23test\3b\3c\3e\2b,dc=\3d\2c\23test\3b\3c\3e\2b,o=\3d\2c\23test\3b\3c\3e\2b',
            $dn->get()
        );
    }

    public function test_add_o()
    {
        $dn = new DistinguishedName();

        $dn->addO('=,#test;<>+');

        $this->assertEquals('o=\3d\2c\23test\3b\3c\3e\2b', $dn->get());
    }

    public function test_remove_o()
    {
        $dn = new DistinguishedName();

        $dn->addO('Testing');

        $dn->removeO('Testing');

        $this->assertEmpty($dn->get());
    }
}
