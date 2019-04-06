<?php

namespace Adldap\tests\Configuration;

use Adldap\Tests\TestCase;
use Adldap\Configuration\DomainConfiguration;

class DomainConfigurationTest extends TestCase
{
    public function test_default()
    {
        $config = new DomainConfiguration();

        $this->assertEquals(389, $config->get('port'));
        $this->assertEquals([], $config->get('hosts'));
        $this->assertEquals(0, $config->get('follow_referrals'));
        $this->assertEmpty($config->get('username'));
        $this->assertEmpty($config->get('password'));
        $this->assertEmpty($config->get('base_dn'));
        $this->assertFalse($config->get('use_ssl'));
        $this->assertFalse($config->get('use_tls'));
        $this->assertEquals([], $config->get('custom_options'));
    }

    public function test_mock_configuration()
    {
        $config = new DomainConfiguration([
            'port'               => 500,
            'base_dn'            => 'dc=corp,dc=org',
            'hosts' => ['dc1', 'dc2'],
            'follow_referrals'   => false,
            'username'     => 'username',
            'password'     => 'password',
            'account_prefix' => 'prefix',
            'account_suffix' => 'suffix',
            'use_ssl'            => true,
            'use_tls'            => false,
            'custom_options'     => [
                LDAP_OPT_SIZELIMIT => 1000
            ]
        ]);

        $this->assertEquals(500, $config->get('port'));
        $this->assertEquals('dc=corp,dc=org', $config->get('base_dn'));
        $this->assertEquals(['dc1', 'dc2'], $config->get('hosts'));
        $this->assertEquals('username', $config->get('username'));
        $this->assertEquals('password', $config->get('password'));
        $this->assertEquals('suffix', $config->get('account_suffix'));
        $this->assertEquals('prefix', $config->get('account_prefix'));
        $this->assertTrue($config->get('use_ssl'));
        $this->assertFalse($config->get('use_tls'));
        $this->assertEquals(
            [
                LDAP_OPT_SIZELIMIT => 1000
            ],
            $config->get('custom_options')
        );
    }

    public function test_invalid_port()
    {
        $this->expectException(\Adldap\Configuration\ConfigurationException::class);

        new DomainConfiguration(['port' => 'invalid']);
    }

    public function test_invalid_base_dn()
    {
        $this->expectException(\Adldap\Configuration\ConfigurationException::class);

        new DomainConfiguration(['base_dn' => ['invalid']]);
    }

    public function test_invalid_domain_controllers()
    {
        $this->expectException(\Adldap\Configuration\ConfigurationException::class);

        new DomainConfiguration(['hosts' => 'invalid']);
    }

    public function test_invalid_admin_username()
    {
        $this->expectException(\Adldap\Configuration\ConfigurationException::class);

        new DomainConfiguration(['admin_username' => ['invalid']]);
    }

    public function test_invalid_admin_password()
    {
        $this->expectException(\Adldap\Configuration\ConfigurationException::class);

        new DomainConfiguration(['admin_password' => ['invalid']]);
    }

    public function test_invalid_admin_account_suffix()
    {
        $this->expectException(\Adldap\Configuration\ConfigurationException::class);

        new DomainConfiguration(['admin_account_suffix' => ['invalid']]);
    }

    public function test_invalid_account_suffix()
    {
        $this->expectException(\Adldap\Configuration\ConfigurationException::class);

        new DomainConfiguration(['account_suffix' => ['invalid']]);
    }

    public function test_invalid_account_prefix()
    {
        $this->expectException(\Adldap\Configuration\ConfigurationException::class);

        new DomainConfiguration(['account_prefix' => ['invalid']]);
    }

    public function test_invalid_follow_referrals()
    {
        $this->expectException(\Adldap\Configuration\ConfigurationException::class);

        new DomainConfiguration(['follow_referrals' => 'invalid']);
    }

    public function test_invalid_use_ssl()
    {
        $this->expectException(\Adldap\Configuration\ConfigurationException::class);

        new DomainConfiguration(['use_ssl' => 'invalid']);
    }

    public function test_invalid_use_tls()
    {
        $this->expectException(\Adldap\Configuration\ConfigurationException::class);

        new DomainConfiguration(['use_tls' => 'invalid']);
    }

    public function test_invalid_custom_options()
    {
        $this->expectException(\Adldap\Configuration\ConfigurationException::class);

        new DomainConfiguration(['custom_options' => 'invalid']);
    }
}
