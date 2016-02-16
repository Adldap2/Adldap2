<?php

namespace Adldap\Tests\Connections;

use Adldap\Connections\Ldap;
use Adldap\Tests\UnitTestCase;

class LdapTest extends UnitTestCase
{
    public function test_construct_defaults()
    {
        $ldap = new Ldap();

        $this->assertFalse($ldap->isUsingTLS());
        $this->assertFalse($ldap->isUsingSSL());
        $this->assertFalse($ldap->isBound());
        $this->assertTrue($ldap->isSupported());
        $this->assertNull($ldap->getConnection());
    }
}
