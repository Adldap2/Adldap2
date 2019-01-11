<?php

namespace Adldap\Tests\Connections;

use Adldap\Query\Builder;
use Adldap\Query\Factory as SearchFactory;
use Adldap\Models\Factory as ModelFactory;
use Adldap\Tests\TestCase;
use Adldap\Connections\Ldap;
use Adldap\Connections\Provider;
use Adldap\Connections\DetailedError;
use Adldap\Connections\ConnectionInterface;
use Adldap\Configuration\DomainConfiguration;

class ProviderTest extends TestCase
{
    protected function newProvider($connection, $configuration = [])
    {
        return new Provider($configuration, $connection);
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

        $error = new DetailedError(42, 'Invalid credentials', '80090308: LdapErr: DSID-0C09042A');

        // Binding fails, retrieves last error.
        $connection->shouldReceive('getLastError')->once()->andReturn('error')
            ->shouldReceive('getDetailedError')->once()->andReturn($error)
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
            'username' => 'test',
            'password' => 'test',
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
            'username' => 'test',
            'password' => 'test',
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
            ->shouldReceive('getDetailedError')->once()->andReturn(new DetailedError(null, null, null))
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
            'username' => 'test',
            'password' => 'test',
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
            ->shouldReceive('get')->withArgs(['hosts'])->once()->andReturn('')
            ->shouldReceive('get')->withArgs(['port'])->once()->andReturn('389')
            ->shouldReceive('get')->withArgs(['schema'])->once()->andReturn('Adldap\Schemas\ActiveDirectory')
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

        $provider = new Provider($config, $connection);

        $this->assertInstanceOf(DomainConfiguration::class, $provider->getConfiguration());
    }

    public function test_groups()
    {
        $m = $this->newProvider(new Ldap());

        $query = $m->search()->groups();

        $this->assertInstanceOf(Builder::class, $query);
        $this->assertEquals('(objectclass=group)', $query->getUnescapedQuery());
    }

    public function test_users()
    {
        $m = $this->newProvider(new Ldap());

        $query = $m->search()->users();

        $this->assertInstanceOf(Builder::class, $query);
        $this->assertEquals('(&(objectclass=user)(objectcategory=person)(!(objectclass=contact)))', $query->getUnescapedQuery());
    }

    public function test_containers()
    {
        $m = $this->newProvider(new Ldap());

        $query = $m->search()->containers();

        $this->assertInstanceOf(Builder::class, $query);
        $this->assertEquals('(objectclass=container)', $query->getUnescapedQuery());
    }

    public function test_contacts()
    {
        $m = $this->newProvider(new Ldap());

        $query = $m->search()->contacts();

        $this->assertInstanceOf(Builder::class, $query);
        $this->assertEquals('(objectclass=contact)', $query->getUnescapedQuery());
    }

    public function test_computers()
    {
        $m = $this->newProvider(new Ldap());

        $query = $m->search()->computers();

        $this->assertInstanceOf(Builder::class, $query);
        $this->assertEquals('(objectclass=computer)', $query->getUnescapedQuery());
    }

    public function test_ous()
    {
        $m = $this->newProvider(new Ldap());

        $query = $m->search()->ous();

        $this->assertInstanceOf(Builder::class, $query);
        $this->assertEquals('(objectclass=organizationalunit)', $query->getUnescapedQuery());
    }

    public function test_printers()
    {
        $m = $this->newProvider(new Ldap());

        $query = $m->search()->printers();

        $this->assertInstanceOf(Builder::class, $query);
        $this->assertEquals('(objectclass=printqueue)', $query->getUnescapedQuery());
    }

    public function test_search()
    {
        $m = $this->newProvider(new Ldap());

        $query = $m->search();

        $this->assertInstanceOf(SearchFactory::class, $query);
        $this->assertEquals('', $query->getUnescapedQuery());
    }

    public function test_make()
    {
        $m = $this->newProvider(new Ldap());

        $this->assertInstanceOf(ModelFactory::class, $m->make());
    }
}
