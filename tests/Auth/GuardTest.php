<?php

namespace Adldap\Tests\Auth;

use Adldap\Auth\Guard;
use Adldap\Tests\TestCase;
use Adldap\Connections\Ldap;
use Adldap\Configuration\DomainConfiguration;

class GuardTest extends TestCase
{
    /**
     * @expectedException \Adldap\Auth\UsernameRequiredException
     */
    public function test_validate_username()
    {
        $guard = new Guard(new Ldap(), new DomainConfiguration());

        $guard->attempt('', 'password');
    }

    /**
     * @expectedException \Adldap\Auth\PasswordRequiredException
     */
    public function test_validate_password()
    {
        $guard = new Guard(new Ldap(), new DomainConfiguration());

        $guard->attempt('username', '');
    }

    public function test_attempt()
    {
        $config = $this->mock(DomainConfiguration::class);

        $config
            ->shouldReceive('get')->withArgs(['account_prefix'])->once()
            ->shouldReceive('get')->withArgs(['account_suffix'])->once()
            ->shouldReceive('get')->withArgs(['admin_username'])->once()
            ->shouldReceive('get')->withArgs(['admin_password'])->once()
            ->shouldReceive('get')->withArgs(['admin_account_prefix'])->once()
            ->shouldReceive('get')->withArgs(['admin_account_suffix'])->once();

        $ldap = $this->mock(Ldap::class);

        $ldap->shouldReceive('bind')->twice()->andReturn(true);

        $guard = new Guard($ldap, $config);

        $this->assertTrue($guard->attempt('username', 'password'));
    }

    public function test_bind_using_credentials()
    {
        $config = $this->mock(DomainConfiguration::class);

        $config
            ->shouldReceive('get')->withArgs(['account_prefix'])->once()->andReturn('prefix-')
            ->shouldReceive('get')->withArgs(['account_suffix'])->once()->andReturn('-suffix');

        $ldap = $this->mock(Ldap::class);

        $ldap->shouldReceive('bind')->once()->withArgs(['prefix-username-suffix', 'password'])->andReturn(true);

        $guard = new Guard($ldap, $config);

        $this->assertNull($guard->bind('username', 'password'));
    }

    /**
     * @expectedException \Adldap\Auth\BindException
     */
    public function test_bind_always_throws_exception_on_invalid_credentials()
    {
        $config = $this->mock(DomainConfiguration::class);

        $config
            ->shouldReceive('get')->withArgs(['account_prefix'])->once()->andReturn('prefix-')
            ->shouldReceive('get')->withArgs(['account_suffix'])->once()->andReturn('-suffix');

        $ldap = $this->mock(Ldap::class);

        $ldap
            ->shouldReceive('bind')->once()->withArgs(['prefix-username-suffix', 'password'])->andReturn(false)
            ->shouldReceive('getLastError')->once()->andReturn('error')
            ->shouldReceive('isUsingSSL')->once()->andReturn(false)
            ->shouldReceive('isUsingTLS')->once()->andReturn(false)
            ->shouldReceive('errNo')->once()->andReturn(1);

        $guard = new Guard($ldap, $config);

        $guard->bind('username', 'password');
    }

    public function test_bind_as_administrator()
    {
        $config = $this->mock(DomainConfiguration::class);

        $config
            ->shouldReceive('get')->withArgs(['admin_username'])->once()->andReturn('admin')
            ->shouldReceive('get')->withArgs(['admin_password'])->once()->andReturn('password')
            ->shouldReceive('get')->withArgs(['admin_account_prefix'])->once()->andReturn('')
            ->shouldReceive('get')->withArgs(['admin_account_suffix'])->once()->andReturn('@admin-suffix')
            ->shouldReceive('get')->withArgs(['account_prefix'])->once()->andReturn('');

        $ldap = $this->mock(Ldap::class);

        $ldap->shouldReceive('bind')->once()->withArgs(['admin@admin-suffix', 'password'])->andReturn(true);

        $guard = new Guard($ldap, $config);

        $this->assertNull($guard->bindAsAdministrator());
    }

    public function test_bind_as_administrator_without_suffix()
    {
        $config = $this->mock(DomainConfiguration::class);

        $config
            ->shouldReceive('get')->withArgs(['admin_username'])->once()->andReturn('admin')
            ->shouldReceive('get')->withArgs(['admin_password'])->once()->andReturn('password')
            ->shouldReceive('get')->withArgs(['admin_account_prefix'])->once()->andReturn(null)
            ->shouldReceive('get')->withArgs(['admin_account_suffix'])->once()->andReturn(null)
            ->shouldReceive('get')->withArgs(['account_prefix'])->once()->andReturn('')
            ->shouldReceive('get')->withArgs(['account_suffix'])->once()->andReturn('@account-suffix');

        $ldap = $this->mock(Ldap::class);

        $ldap->shouldReceive('bind')->once()->withArgs(['admin@account-suffix', 'password'])->andReturn(true);

        $guard = new Guard($ldap, $config);

        $this->assertNull($guard->bindAsAdministrator());
    }
}
