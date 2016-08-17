<?php

namespace Adldap\Tests\Auth;

use Adldap\Auth\Guard;
use Adldap\Connections\Configuration;
use Adldap\Connections\Ldap;
use Adldap\Exceptions\Auth\BindException;
use Adldap\Tests\TestCase;

class GuardTest extends TestCase
{
    public function test_validate_username()
    {
        $guard = new Guard(new Ldap(), new Configuration());

        $this->setExpectedException('Adldap\Exceptions\Auth\UsernameRequiredException');

        $guard->attempt('', 'password');
    }

    public function test_validate_password()
    {
        $guard = new Guard(new Ldap(), new Configuration());

        $this->setExpectedException('Adldap\Exceptions\Auth\PasswordRequiredException');

        $guard->attempt('username', '');
    }

    public function test_attempt()
    {
        $config = $this->mock(Configuration::class);

        $config
            ->shouldReceive('getAccountPrefix')->once()->andReturn('prefix')
            ->shouldReceive('getAccountSuffix')->once()->andReturn('suffix')
            ->shouldReceive('getAdminCredentials')->once()->andReturn(['username', 'password', 'suffix']);

        $ldap = $this->mock(Ldap::class);

        $ldap->shouldReceive('bind')->twice()->andReturn(true);

        $guard = new Guard($ldap, $config);

        $this->assertTrue($guard->attempt('username', 'password'));
    }

    public function test_bind_using_credentials()
    {
        $config = $this->mock(Configuration::class);

        $config
            ->shouldReceive('getAccountPrefix')->once()->andReturn('prefix-')
            ->shouldReceive('getAccountSuffix')->once()->andReturn('-suffix');

        $ldap = $this->mock(Ldap::class);

        $ldap->shouldReceive('bind')->once()->withArgs(['prefix-username-suffix', 'password'])->andReturn(true);

        $guard = new Guard($ldap, $config);

        $this->assertNull($guard->bind('username', 'password'));
    }

    public function test_bind_always_throws_exception_on_invalid_credentials()
    {
        $config = $this->mock(Configuration::class);

        $config
            ->shouldReceive('getAccountPrefix')->once()->andReturn('prefix-')
            ->shouldReceive('getAccountSuffix')->once()->andReturn('-suffix');

        $ldap = $this->mock(Ldap::class);

        $ldap
            ->shouldReceive('bind')->once()->withArgs(['prefix-username-suffix', 'password'])->andReturn(false)
            ->shouldReceive('getLastError')->once()->andReturn('error')
            ->shouldReceive('isUsingSSL')->once()->andReturn(false)
            ->shouldReceive('isUsingTLS')->once()->andReturn(false)
            ->shouldReceive('errNo')->once()->andReturn(1);

        $guard = new Guard($ldap, $config);

        $this->setExpectedException(BindException::class);

        $guard->bind('username', 'password');
    }

    public function test_bind_as_administrator()
    {
        $config = $this->mock(Configuration::class);

        $config->shouldReceive('getAdminCredentials')->once()->andReturn(['admin', 'password', '@admin-suffix']);

        $ldap = $this->mock(Ldap::class);

        $ldap->shouldReceive('bind')->once()->withArgs(['admin@admin-suffix', 'password'])->andReturn(true);

        $guard = new Guard($ldap, $config);

        $this->assertNull($guard->bindAsAdministrator());
    }

    public function test_bind_as_administrator_without_suffix()
    {
        $config = $this->mock(Configuration::class);

        $config
            ->shouldReceive('getAdminCredentials')->once()->andReturn(['admin', 'password', null])
            ->shouldReceive('getAccountSuffix')->once()->andReturn('@account-suffix');

        $ldap = $this->mock(Ldap::class);

        $ldap->shouldReceive('bind')->once()->withArgs(['admin@account-suffix', 'password'])->andReturn(true);

        $guard = new Guard($ldap, $config);

        $this->assertNull($guard->bindAsAdministrator());
    }
}
