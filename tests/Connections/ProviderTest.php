<?php

namespace Adldap\Tests\Connections;

use Adldap\Query\Builder;
use Adldap\Tests\TestCase;
use Adldap\Search\Factory;
use Adldap\Connections\Ldap;
use Adldap\Connections\Provider;
use Adldap\Connections\Configuration;
use Adldap\Exceptions\Auth\BindException;
use Adldap\Exceptions\Auth\UsernameRequiredException;
use Adldap\Exceptions\Auth\PasswordRequiredException;
use Adldap\Contracts\Connections\ConnectionInterface;

class ProviderTest extends TestCase
{
    protected function newProvider($connection, $configuration, $schema = null)
    {
        return new Provider($configuration, $connection, $schema);
    }

    public function test_construct()
    {
        $m = $this->newProvider(new Ldap(), new Configuration());

        $this->assertInstanceOf(ConnectionInterface::class, $m->getConnection());
        $this->assertInstanceOf(Configuration::class, $m->getConfiguration());
    }

    public function test_auth_username_failure()
    {
        $connection = $this->newConnectionMock();

        $connection
            ->shouldReceive('setOption')->twice()
            ->shouldReceive('connect')->once()
            ->shouldReceive('isBound')->once()->andReturn(true)
            ->shouldReceive('close')->once()->andReturn(true);

        $m = $this->newProvider($connection, new Configuration());

        $this->setExpectedException(UsernameRequiredException::class);

        $m->auth()->attempt(0000000, 'password');
    }

    public function test_auth_password_failure()
    {
        $connection = $this->newConnectionMock();

        $connection
            ->shouldReceive('setOption')->twice()
            ->shouldReceive('connect')->once()
            ->shouldReceive('isBound')->once()->andReturn(true)
            ->shouldReceive('close')->once()->andReturn(true);

        $m = $this->newProvider($connection, new Configuration());

        $this->setExpectedException(PasswordRequiredException::class);

        $m->auth()->attempt('username', 0000000);
    }

    public function test_auth_failure()
    {
        $connection = $this->newConnectionMock();

        $connection
            ->shouldReceive('connect')->once()->andReturn(true)
            ->shouldReceive('setOption')->twice()->andReturn(true)
            ->shouldReceive('isUsingSSL')->once()->andReturn(false)
            ->shouldReceive('isBound')->once()->andReturn(true)
            ->shouldReceive('bind')->once()->withArgs(['username', 'password'])->andReturn(false)
            ->shouldReceive('getLastError')->once()->andReturn('error')
            ->shouldReceive('isBound')->once()->andReturn(true)
            ->shouldReceive('errNo')->once()->andReturn(1)
            ->shouldReceive('close')->once()->andReturn(true);

        $m = $this->newProvider($connection, new Configuration());

        $this->assertFalse($m->auth()->attempt('username', 'password'));
    }

    public function test_auth_passes_with_rebind()
    {
        $config = new Configuration();

        $config->setAdminUsername('test');
        $config->setAdminPassword('test');

        $connection = $this->newConnectionMock();

        $connection
            ->shouldReceive('connect')->once()->andReturn(true)
            ->shouldReceive('setOption')->twice()->andReturn(true)
            ->shouldReceive('isUsingSSL')->once()->andReturn(false)
            ->shouldReceive('isBound')->once()->andReturn(true);

        // Authenticates as the user
        $connection->shouldReceive('bind')->once()->withArgs(['username', 'password'])->andReturn(true);

        // Re-binds as the administrator
        $connection
            ->shouldReceive('bind')->once()->withArgs(['test', 'test'])->andReturn(true)
            ->shouldReceive('isBound')->once()->andReturn(true)
            ->shouldReceive('close')->once()->andReturn(true);

        $m = $this->newProvider($connection, $config);

        $this->assertTrue($m->auth()->attempt('username', 'password'));
    }

    public function test_auth_rebind_failure()
    {
        $config = new Configuration();

        $config->setAdminUsername('test');
        $config->setAdminPassword('test');

        $connection = $this->newConnectionMock();

        $connection
            ->shouldReceive('connect')->once()->andReturn(true)
            ->shouldReceive('setOption')->twice()->andReturn(true)
            ->shouldReceive('isUsingSSL')->once()->andReturn(false)
            ->shouldReceive('isBound')->once()->andReturn(true);

        // Authenticates as the user
        $connection->shouldReceive('bind')->once()->withArgs(['username', 'password']);

        // Re-binds as the administrator (fails)
        $connection->shouldReceive('bind')->once()->withArgs(['test', 'test'])->andReturn(false)
            ->shouldReceive('getLastError')->once()->andReturn('')
            ->shouldReceive('isBound')->once()->andReturn(true)
            ->shouldReceive('errNo')->once()->andReturn(1)
            ->shouldReceive('close')->once()->andReturn(true);

        $m = $this->newProvider($connection, $config);

        $this->setExpectedException(BindException::class);

        $m->connect();

        $this->assertTrue($m->auth()->attempt('username', 'password'));
    }

    public function test_auth_passes_without_rebind()
    {
        $config = new Configuration();

        $config->setAdminUsername('test');
        $config->setAdminPassword('test');

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('connect')->once()->andReturn(true)
            ->shouldReceive('setOption')->twice()->andReturn(true)
            ->shouldReceive('isUsingSSL')->once()->andReturn(false)
            ->shouldReceive('isBound')->once()->andReturn(true)
            ->shouldReceive('bind')->once()->withArgs(['username', 'password'])->andReturn(true)
            ->shouldReceive('getLastError')->once()->andReturn('')
            ->shouldReceive('isBound')->once()->andReturn(true)
            ->shouldReceive('close')->once()->andReturn(true);

        $m = $this->newProvider($connection, $config);

        $this->assertTrue($m->auth()->attempt('username', 'password', true));
    }

    public function test_groups()
    {
        $m = $this->newProvider(new Ldap(), new Configuration());

        $this->assertInstanceOf(Builder::class, $m->search()->groups());
    }

    public function test_users()
    {
        $m = $this->newProvider(new Ldap(), new Configuration());

        $this->assertInstanceOf(Builder::class, $m->search()->users());
    }

    public function test_containers()
    {
        $m = $this->newProvider(new Ldap(), new Configuration());

        $this->assertInstanceOf(Builder::class, $m->search()->containers());
    }

    public function test_contacts()
    {
        $m = $this->newProvider(new Ldap(), new Configuration());

        $this->assertInstanceOf(Builder::class, $m->search()->contacts());
    }

    public function test_computers()
    {
        $m = $this->newProvider(new Ldap(), new Configuration());

        $this->assertInstanceOf(Builder::class, $m->search()->computers());
    }

    public function test_ous()
    {
        $m = $this->newProvider(new Ldap(), new Configuration());

        $this->assertInstanceOf(Builder::class, $m->search()->contacts());
    }

    public function test()
    {
        $m = $this->newProvider(new Ldap(), new Configuration());

        $this->assertInstanceOf(Builder::class, $m->search()->contacts());
    }

    public function test_printers()
    {
        $m = $this->newProvider(new Ldap(), new Configuration());

        $this->assertInstanceOf(Builder::class, $m->search()->printers());
    }

    public function test_search()
    {
        $m = $this->newProvider(new Ldap(), new Configuration());

        $this->assertInstanceOf(Factory::class, $m->search());
    }
}
