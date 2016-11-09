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

    public function test_get_default_protocol()
    {
        $ldap = new Ldap();

        $this->assertEquals('ldap://', $ldap->getProtocol());
    }

    public function test_get_protocol_ssl()
    {
        $ldap = new Ldap();

        $ldap->ssl();

        $this->assertEquals('ldaps://', $ldap->getProtocol());
    }

    public function test_can_change_passwords()
    {
        $ldap = new Ldap();

        $ldap->ssl();

        $this->assertTrue($ldap->canChangePasswords());

        $ldap->ssl(false);

        $this->assertFalse($ldap->canChangePasswords());

        $ldap->tls();

        $this->assertTrue($ldap->canChangePasswords());
    }

    public function test_set_options()
    {
        $ldap = new Ldap();

        $ldap->setOptions([1 => 'value', 2 => 'value']);
    }
}
