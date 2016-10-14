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
            'use_ssl'            => true,
            'use_tls'            => false,
        ]);

        $this->assertEquals(500, $config->get('port'));
        $this->assertEquals('dc=corp,dc=org', $config->get('base_dn'));
        $this->assertEquals(['dc1', 'dc2'], $config->get('domain_controllers'));
        $this->assertEquals('username', $config->get('admin_username'));
        $this->assertEquals('password', $config->get('admin_password'));
        $this->assertTrue($config->get('use_ssl'));
        $this->assertFalse($config->get('use_tls'));
    }
}
