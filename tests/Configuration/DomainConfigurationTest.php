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
        $this->assertEquals([], $config->get('domain_controllers'));
        $this->assertEquals(0, $config->get('follow_referrals'));
        $this->assertEmpty($config->get('admin_username'));
        $this->assertEmpty($config->get('admin_password'));
        $this->assertEmpty($config->get('base_dn'));
        $this->assertFalse($config->get('use_ssl'));
        $this->assertFalse($config->get('use_tls'));
        $this->assertEquals([], $config->get('custom_options'));
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
            'admin_account_prefix' => 'admin-prefix',
            'admin_account_suffix' => 'admin-suffix',
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
        $this->assertEquals(['dc1', 'dc2'], $config->get('domain_controllers'));
        $this->assertEquals('username', $config->get('admin_username'));
        $this->assertEquals('password', $config->get('admin_password'));
        $this->assertEquals('admin-prefix', $config->get('admin_account_prefix'));
        $this->assertEquals('admin-suffix', $config->get('admin_account_suffix'));
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

    /**
     * @expectedException \Adldap\Configuration\ConfigurationException
     */
    public function test_invalid_port()
    {
        new DomainConfiguration(['port' => 'invalid']);
    }

    /**
     * @expectedException \Adldap\Configuration\ConfigurationException
     */
    public function test_invalid_base_dn()
    {
        new DomainConfiguration(['base_dn' => ['invalid']]);
    }

    /**
     * @expectedException \Adldap\Configuration\ConfigurationException
     */
    public function test_invalid_domain_controllers()
    {
        new DomainConfiguration(['domain_controllers' => 'invalid']);
    }

    /**
     * @expectedException \Adldap\Configuration\ConfigurationException
     */
    public function test_invalid_admin_username()
    {
        new DomainConfiguration(['admin_username' => ['invalid']]);
    }

    /**
     * @expectedException \Adldap\Configuration\ConfigurationException
     */
    public function test_invalid_admin_password()
    {
        new DomainConfiguration(['admin_password' => ['invalid']]);
    }

    /**
     * @expectedException \Adldap\Configuration\ConfigurationException
     */
    public function test_invalid_admin_account_suffix()
    {
        new DomainConfiguration(['admin_account_suffix' => ['invalid']]);
    }

    /**
     * @expectedException \Adldap\Configuration\ConfigurationException
     */
    public function test_invalid_account_suffix()
    {
        new DomainConfiguration(['account_suffix' => ['invalid']]);
    }

    /**
     * @expectedException \Adldap\Configuration\ConfigurationException
     */
    public function test_invalid_account_prefix()
    {
        new DomainConfiguration(['account_prefix' => ['invalid']]);
    }

    /**
     * @expectedException \Adldap\Configuration\ConfigurationException
     */
    public function test_invalid_follow_referrals()
    {
        new DomainConfiguration(['follow_referrals' => 'invalid']);
    }

    /**
     * @expectedException \Adldap\Configuration\ConfigurationException
     */
    public function test_invalid_use_ssl()
    {
        new DomainConfiguration(['use_ssl' => 'invalid']);
    }

    /**
     * @expectedException \Adldap\Configuration\ConfigurationException
     */
    public function test_invalid_use_tls()
    {
        new DomainConfiguration(['use_tls' => 'invalid']);
    }

    /**
     * @expectedException \Adldap\Configuration\ConfigurationException
     */
    public function test_invalid_custom_options()
    {
        new DomainConfiguration(['custom_options' => 'invalid']);
    }
}
