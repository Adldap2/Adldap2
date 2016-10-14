<?php

namespace Adldap\tests\Configuration;

use Adldap\Configuration\ConfigurationException;
use Adldap\Tests\TestCase;
use Adldap\Configuration\DomainConfiguration;

class DomainConfigurationTest extends TestCase
{
    public function test_default()
    {
        $config = new DomainConfiguration();

        $this->assertEquals(389, $config->get('port'));
        $this->assertEquals([], $config->get('domain_controllers'));
        $this->assertEquals(0, $config->get('follow_referrals'));
        $this->assertEmpty($config->get('admin_username'));
        $this->assertEmpty($config->get('admin_password'));
        $this->assertEmpty($config->get('base_dn'));
        $this->assertFalse($config->get('use_ssl'));
        $this->assertFalse($config->get('use_tls'));
    }

    public function test_construct()
    {
        $config = new DomainConfiguration([
            'port'               => 500,
            'base_dn'            => 'dc=corp,dc=org',
            'domain_controllers' => ['dc1', 'dc2'],
            'follow_referrals'   => false,
            'admin_username'     => 'username',
            'admin_password'     => 'password',
            'admin_account_suffix' => 'suffix',
            'account_suffix' => 'suffix',
            'account_prefix' => 'prefix',
            'use_ssl'            => true,
            'use_tls'            => false,
        ]);

        $this->assertEquals(500, $config->get('port'));
        $this->assertEquals('dc=corp,dc=org', $config->get('base_dn'));
        $this->assertEquals(['dc1', 'dc2'], $config->get('domain_controllers'));
        $this->assertEquals('username', $config->get('admin_username'));
        $this->assertEquals('password', $config->get('admin_password'));
        $this->assertEquals('suffix', $config->get('admin_account_suffix'));
        $this->assertEquals('suffix', $config->get('account_suffix'));
        $this->assertEquals('prefix', $config->get('account_prefix'));
        $this->assertTrue($config->get('use_ssl'));
        $this->assertFalse($config->get('use_tls'));
    }

    public function test_invalid_port()
    {
        $this->setExpectedException(ConfigurationException::class);

        new DomainConfiguration(['port' => 'invalid']);
    }

    public function test_invalid_base_dn()
    {
        $this->setExpectedException(ConfigurationException::class);

        new DomainConfiguration(['base_dn' => ['invalid']]);
    }

    public function test_invalid_domain_controllers()
    {
        $this->setExpectedException(ConfigurationException::class);

        new DomainConfiguration(['domain_controllers' => 'invalid']);
    }

    public function test_invalid_admin_username()
    {
        $this->setExpectedException(ConfigurationException::class);

        new DomainConfiguration(['admin_username' => ['invalid']]);
    }

    public function test_invalid_admin_password()
    {
        $this->setExpectedException(ConfigurationException::class);

        new DomainConfiguration(['admin_password' => ['invalid']]);
    }

    public function test_invalid_admin_account_suffix()
    {
        $this->setExpectedException(ConfigurationException::class);

        new DomainConfiguration(['admin_account_suffix' => ['invalid']]);
    }

    public function test_invalid_account_suffix()
    {
        $this->setExpectedException(ConfigurationException::class);

        new DomainConfiguration(['account_suffix' => ['invalid']]);
    }

    public function test_invalid_account_prefix()
    {
        $this->setExpectedException(ConfigurationException::class);

        new DomainConfiguration(['account_prefix' => ['invalid']]);
    }

    public function test_invalid_follow_referrals()
    {
        $this->setExpectedException(ConfigurationException::class);

        new DomainConfiguration(['follow_referrals' => 'invalid']);
    }

    public function test_invalid_use_ssl()
    {
        $this->setExpectedException(ConfigurationException::class);

        new DomainConfiguration(['use_ssl' => 'invalid']);
    }

    public function test_invalid_use_tls()
    {
        $this->setExpectedException(ConfigurationException::class);

        new DomainConfiguration(['use_tls' => 'invalid']);
    }
}
