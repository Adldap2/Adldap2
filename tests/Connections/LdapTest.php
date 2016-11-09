<?php

namespace Adldap\Tests\Connections;

use Adldap\Tests\TestCase;
use Adldap\Connections\Ldap;

class LdapTest extends TestCase
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

    public function test_connections_string_with_array()
    {
        $ldap = $this->mock(Ldap::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $connections = $ldap->getConnectionString([
            'dc01',
            'dc02',
        ], 'ldap://', '389');

        $this->assertEquals('ldap://dc01:389 ldap://dc02:389', $connections);
    }

    public function test_connections_string_with_string()
    {
        $ldap = $this->mock(Ldap::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $connection = $ldap->getConnectionString('dc01', 'ldap://', '389');

        $this->assertEquals('ldap://dc01:389', $connection);
    }

    public function test_get_protocol()
    {
        $ldap = new Ldap();

        $ldap->useSSL();

        $this->assertEquals('ldaps://', $ldap->getProtocol());
    }
}
