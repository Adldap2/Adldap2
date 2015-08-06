<?php

namespace Adldap\tests\Connections;

use Adldap\Connections\Ldap;
use Adldap\Tests\UnitTestCase;

class LdapTest extends UnitTestCase
{
    public function testConstructDefaults()
    {
        $ldap = new Ldap();

        $this->assertFalse($ldap->isUsingTLS());
        $this->assertFalse($ldap->isUsingSSL());
        $this->assertFalse($ldap->isUsingSSO());
        $this->assertFalse($ldap->isBound());
        $this->assertTrue($ldap->isSupported());
        $this->assertNull($ldap->getConnection());
    }

    public function testSuppressErrors()
    {
        $ldap = new Ldap();

        $ldap->suppressErrors();

        $this->assertNull($ldap->add('', []));

        $ldap->showErrors();

        try {
            $ldap->add('', []);

            $passes = false;
        } catch (\Exception $e) {
            $passes = true;
        }

        $this->assertTrue($passes);
    }
}
