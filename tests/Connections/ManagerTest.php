<?php

namespace Adldap\Tests\Connections;

use Adldap\Connections\Configuration;
use Adldap\Connections\Ldap;
use Adldap\Connections\Manager;
use Adldap\Schemas\Schema;
use Adldap\Tests\UnitTestCase;

class ManagerTest extends UnitTestCase
{
    protected function newManager($connection, $configuration, $schema = null)
    {
        if (is_null($schema)) $schema = Schema::get();

        return new Manager($connection, $configuration, $schema);
    }

    public function testConstruct()
    {
        $m = $this->newManager(new Ldap(), new Configuration());

        $this->assertInstanceOf('Adldap\Contracts\Connections\ConnectionInterface', $m->getConnection());
        $this->assertInstanceOf('Adldap\Connections\Configuration', $m->getConfiguration());
    }

    public function testAuthUsernameFailure()
    {
        $connection = $this->newConnectionMock();

        $connection->shouldReceive('isBound')->once()->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $m = $this->newManager($connection, new Configuration());

        $this->setExpectedException('Adldap\Exceptions\Auth\UsernameRequiredException');

        $m->auth()->attempt(' ', 'password');
    }

    public function testAuthPasswordFailure()
    {
        $connection = $this->newConnectionMock();

        $connection->shouldReceive('isBound')->once()->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $m = $this->newManager($connection, new Configuration());

        $this->setExpectedException('Adldap\Exceptions\Auth\PasswordRequiredException');

        $m->auth()->attempt('username', ' ');
    }

    public function testAuthFailure()
    {
        $connection = $this->newConnectionMock();

        $connection->shouldReceive('connect')->once()->andReturn(true);
        $connection->shouldReceive('setOption')->twice()->andReturn(true);
        $connection->shouldReceive('isUsingSSL')->once()->andReturn(false);
        $connection->shouldReceive('isBound')->once()->andReturn(true);
        $connection->shouldReceive('bind')->once()->withArgs(['username', 'password'])->andThrow('Exception');
        $connection->shouldReceive('getLastError')->once()->andReturn('');
        $connection->shouldReceive('isBound')->once()->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $m = $this->newManager($connection, new Configuration());

        $this->assertFalse($m->auth()->attempt('username', 'password'));
    }

    public function testAuthPassesWithRebind()
    {
        $config = new Configuration();

        $config->setAdminUsername('test');
        $config->setAdminPassword('test');

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('connect')->once()->andReturn(true);
        $connection->shouldReceive('setOption')->twice()->andReturn(true);
        $connection->shouldReceive('isUsingSSL')->once()->andReturn(false);
        $connection->shouldReceive('isBound')->once()->andReturn(true);

        // Authenticates as the user
        $connection->shouldReceive('bind')->once()->withArgs(['username', 'password'])->andReturn(true);

        // Re-binds as the administrator
        $connection->shouldReceive('bind')->once()->withArgs(['test', 'test'])->andReturn(true);
        $connection->shouldReceive('getLastError')->once()->andReturn('');
        $connection->shouldReceive('isBound')->once()->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $m = $this->newManager($connection, $config);

        $this->assertTrue($m->auth()->attempt('username', 'password'));
    }

    public function testAuthRebindFailure()
    {
        $config = new Configuration();

        $config->setAdminUsername('test');
        $config->setAdminPassword('test');

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('connect')->once()->andReturn(true);
        $connection->shouldReceive('setOption')->twice()->andReturn(true);
        $connection->shouldReceive('isUsingSSL')->once()->andReturn(false);
        $connection->shouldReceive('isBound')->once()->andReturn(true);

        // Authenticates as the user
        $connection->shouldReceive('bind')->once()->withArgs(['username', 'password']);

        // Re-binds as the administrator (fails)
        $connection->shouldReceive('bind')->once()->withArgs(['test', 'test'])->andThrow('Exception');
        $connection->shouldReceive('getLastError')->once()->andReturn('');
        $connection->shouldReceive('isBound')->once()->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $m = $this->newManager($connection, $config);

        $this->setExpectedException('Adldap\Exceptions\Auth\BindException');

        $m->auth()->attempt('username', 'password');
    }

    public function testAuthPassesWithoutRebind()
    {
        $config = new Configuration();

        $config->setAdminUsername('test');
        $config->setAdminPassword('test');

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('connect')->once()->andReturn(true);
        $connection->shouldReceive('setOption')->twice()->andReturn(true);
        $connection->shouldReceive('isUsingSSL')->once()->andReturn(false);
        $connection->shouldReceive('isBound')->once()->andReturn(true);
        $connection->shouldReceive('bind')->once()->withArgs(['username', 'password'])->andReturn(true);
        $connection->shouldReceive('getLastError')->once()->andReturn('');
        $connection->shouldReceive('isBound')->once()->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $m = $this->newManager($connection, $config);

        $this->assertTrue($m->auth()->attempt('username', 'password', true));
    }

    public function testGroups()
    {
        $m = $this->newManager(new Ldap(), new Configuration());

        $this->assertInstanceOf('Adldap\Query\Builder', $m->search()->groups());
    }

    public function testUsers()
    {
        $m = $this->newManager(new Ldap(), new Configuration());

        $this->assertInstanceOf('Adldap\Query\Builder', $m->search()->users());
    }

    public function testContainers()
    {
        $m = $this->newManager(new Ldap(), new Configuration());

        $this->assertInstanceOf('Adldap\Query\Builder', $m->search()->containers());
    }

    public function testContacts()
    {
        $m = $this->newManager(new Ldap(), new Configuration());

        $this->assertInstanceOf('Adldap\Query\Builder', $m->search()->contacts());
    }

    public function testExchangeServers()
    {
        $connection = $this->newConnectionMock();

        $connection->shouldReceive('read')->once()->andReturn($connection);
        $connection->shouldReceive('getEntries')->once()->andReturn([
            'count' => 1,
            [
                'cn' => ['Test'],
                'dn' => 'cn=Test,dc=corp,dc=acme,dc=org',
                'configurationnamingcontext' => ['Test'],
            ],
        ]);
        $connection->shouldReceive('isBound')->once()->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $m = $this->newManager($connection, new Configuration());

        $this->assertInstanceOf('Adldap\Query\Builder', $m->search()->exchangeServers());
    }

    public function testComputers()
    {
        $m = $this->newManager(new Ldap(), new Configuration());

        $this->assertInstanceOf('Adldap\Query\Builder', $m->search()->computers());
    }

    public function testOus()
    {
        $m = $this->newManager(new Ldap(), new Configuration());

        $this->assertInstanceOf('Adldap\Query\Builder', $m->search()->contacts());
    }

    public function test()
    {
        $m = $this->newManager(new Ldap(), new Configuration());

        $this->assertInstanceOf('Adldap\Query\Builder', $m->search()->contacts());
    }

    public function testPrinters()
    {
        $m = $this->newManager(new Ldap(), new Configuration());

        $this->assertInstanceOf('Adldap\Query\Builder', $m->search()->printers());
    }

    public function testSearch()
    {
        $m = $this->newManager(new Ldap(), new Configuration());

        $this->assertInstanceOf('Adldap\Search\Factory', $m->search());
    }
}
