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

    public function test_get_host()
    {
        $ldap = new Ldap();

        $ldap->connect('192.168.1.1');

        $this->assertEquals('ldap://192.168.1.1:389', $ldap->getHost());
    }

    public function test_get_host_is_null_without_connecting()
    {
        $ldap = new Ldap();

        $this->assertNull($ldap->getHost());
    }

    public function test_connections_can_be_named()
    {
        $ldap = new Ldap('domain-a');

        $this->assertEquals('domain-a', $ldap->getName());
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
        $ldap = $this->getMockBuilder(Ldap::class)->setMethods(['setOption'])->getMock();

        $ldap->expects($this->exactly(2))->method('setOption');

        $ldap->setOptions([1 => 'value', 2 => 'value']);
    }

    public function test_get_detailed_error_returns_null_when_error_number_is_zero()
    {
        $ldap = $this->mock(Ldap::class)->makePartial();

        $ldap->shouldReceive('errNo')->once()->andReturn(0);

        $this->assertNull($ldap->getDetailedError());
    }
}
