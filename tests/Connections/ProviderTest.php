<?php

namespace Adldap\Tests\Connections;

use Adldap\Query\Builder;
use Adldap\Tests\TestCase;
use Adldap\Search\Factory;
use Adldap\Connections\Ldap;
use Adldap\Connections\Provider;
use Adldap\Connections\ConnectionInterface;
use Adldap\Configuration\DomainConfiguration;

class ProviderTest extends TestCase
{
    protected function newProvider($connection, $configuration = [], $schema = null)
    {
        return new Provider($configuration, $connection, $schema);
    }

    public function test_construct()
    {
        $m = $this->newProvider(new Ldap(), new DomainConfiguration());

        $this->assertInstanceOf(ConnectionInterface::class, $m->getConnection());
        $this->assertInstanceOf(DomainConfiguration::class, $m->getConfiguration());
    }

    /**
     * @expectedException \Adldap\Auth\UsernameRequiredException
     */
    public function test_auth_username_failure()
    {
        $connection = $this->newConnectionMock();

        $connection
            ->shouldReceive('setOptions')->once()
            ->shouldReceive('connect')->once()
            ->shouldReceive('isBound')->once()->andReturn(true)
            ->shouldReceive('close')->once()->andReturn(true);

        $m = $this->newProvider($connection);

        $m->auth()->attempt(0000000, 'password');
    }

    /**
     * @expectedException \Adldap\Auth\PasswordRequiredException
     */
    public function test_auth_password_failure()
    {
        $connection = $this->newConnectionMock();

        $connection
            ->shouldReceive('setOptions')->once()
            ->shouldReceive('connect')->once()
            ->shouldReceive('isBound')->once()->andReturn(true)
            ->shouldReceive('close')->once()->andReturn(true);

        $m = $this->newProvider($connection);

        $m->auth()->attempt('username', 0000000);
    }

    public function test_auth_failure()
    {
        $connection = $this->newConnectionMock();

        // Binding as the user.
        $connection
            ->shouldReceive('connect')->once()->andReturn(true)
            ->shouldReceive('setOptions')->once()
            ->shouldReceive('bind')->once()->withArgs(['username', 'password'])->andReturn(false);

        // Binding fails, retrieves last error.
        $connection->shouldReceive('getLastError')->once()->andReturn('error')
            ->shouldReceive('isBound')->once()->andReturn(true)
            ->shouldReceive('errNo')->once()->andReturn(1);

        // Rebinds as the administrator.
        $connection->shouldReceive('bind')->once()->withArgs([null, null])->andReturn(true);

        // Closes the connection.
        $connection->shouldReceive('close')->once()->andReturn(true);

        $m = $this->newProvider($connection);

        $this->assertFalse($m->auth()->attempt('username', 'password'));
    }

    public function test_auth_passes_with_rebind()
    {
        $config = new DomainConfiguration([
            'admin_username' => 'test',
            'admin_password' => 'test',
        ]);

        $connection = $this->newConnectionMock();

        $connection
            ->shouldReceive('connect')->once()->andReturn(true)
            ->shouldReceive('setOptions')->once()
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

    /**
     * @expectedException \Adldap\Auth\BindException
     */
    public function test_auth_rebind_failure()
    {
        $config = new DomainConfiguration([
            'admin_username' => 'test',
            'admin_password' => 'test',
        ]);

        $connection = $this->newConnectionMock();

        $connection
            ->shouldReceive('connect')->once()->andReturn(true)
            ->shouldReceive('setOptions')->once()
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

        $m->connect();

        $this->assertTrue($m->auth()->attempt('username', 'password'));
    }

    public function test_auth_passes_without_rebind()
    {
        $config = new DomainConfiguration([
            'admin_username' => 'test',
            'admin_password' => 'test',
        ]);

        $connection = $this->newConnectionMock();

        $connection->shouldReceive('connect')->once()->andReturn(true)
            ->shouldReceive('setOptions')->once()
            ->shouldReceive('isUsingSSL')->once()->andReturn(false)
            ->shouldReceive('isBound')->once()->andReturn(true)
            ->shouldReceive('bind')->once()->withArgs(['username', 'password'])->andReturn(true)
            ->shouldReceive('getLastError')->once()->andReturn('')
            ->shouldReceive('isBound')->once()->andReturn(true)
            ->shouldReceive('close')->once()->andReturn(true);

        $m = $this->newProvider($connection, $config);

        $this->assertTrue($m->auth()->attempt('username', 'password', true));
    }

    public function test_prepare_connection()
    {
        $config = $this->mock(DomainConfiguration::class);

        $config
            ->shouldReceive('get')->withArgs(['domain_controllers'])->once()->andReturn('')
            ->shouldReceive('get')->withArgs(['port'])->once()->andReturn('389')
            ->shouldReceive('get')->withArgs(['use_ssl'])->once()->andReturn(false)
            ->shouldReceive('get')->withArgs(['use_tls'])->once()->andReturn(false)
            ->shouldReceive('get')->withArgs(['version'])->once()->andReturn(3)
            ->shouldReceive('get')->withArgs(['timeout'])->once()->andReturn(5)
            ->shouldReceive('get')->withArgs(['follow_referrals'])->andReturn(false)
            // Setting LDAP_OPT_PROTOCOL_VERSION to "2" here enforces the documented behavior of honoring the
            // "version" key over LDAP_OPT_PROTOCOL_VERSION in custom_options.
            ->shouldReceive('get')->withArgs(['custom_options'])->andReturn([LDAP_OPT_PROTOCOL_VERSION => 2]);

        $connection = $this->mock(ConnectionInterface::class);

        $connection
            ->shouldReceive('setOptions')->once()->withArgs([[
                LDAP_OPT_PROTOCOL_VERSION => 3,
                LDAP_OPT_NETWORK_TIMEOUT => 5,
                LDAP_OPT_REFERRALS => false,
            ]])
            ->shouldReceive('connect')->once()
            ->shouldReceive('isBound')->once()->andReturn(false);

        new Provider($config, $connection);
    }

    public function test_groups()
    {
        $m = $this->newProvider(new Ldap());

        $this->assertInstanceOf(Builder::class, $m->search()->groups());
    }

    public function test_users()
    {
        $m = $this->newProvider(new Ldap());

        $this->assertInstanceOf(Builder::class, $m->search()->users());
    }

    public function test_containers()
    {
        $m = $this->newProvider(new Ldap());

        $this->assertInstanceOf(Builder::class, $m->search()->containers());
    }

    public function test_contacts()
    {
        $m = $this->newProvider(new Ldap());

        $this->assertInstanceOf(Builder::class, $m->search()->contacts());
    }

    public function test_computers()
    {
        $m = $this->newProvider(new Ldap());

        $this->assertInstanceOf(Builder::class, $m->search()->computers());
    }

    public function test_ous()
    {
        $m = $this->newProvider(new Ldap());

        $this->assertInstanceOf(Builder::class, $m->search()->contacts());
    }

    public function test()
    {
        $m = $this->newProvider(new Ldap());

        $this->assertInstanceOf(Builder::class, $m->search()->contacts());
    }

    public function test_printers()
    {
        $m = $this->newProvider(new Ldap());

        $this->assertInstanceOf(Builder::class, $m->search()->printers());
    }

    public function test_search()
    {
        $m = $this->newProvider(new Ldap());

        $this->assertInstanceOf(Factory::class, $m->search());
    }
}
