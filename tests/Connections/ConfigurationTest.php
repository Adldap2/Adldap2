<?php

namespace Adldap\Tests\Connections;

use Adldap\Connections\Configuration;
use Adldap\Tests\UnitTestCase;

class ConfigurationTest extends UnitTestCase
{
    public function test_defaults()
    {
        $config = new Configuration();

        $this->assertEquals(389, $config->getPort());
        $this->assertEquals([], $config->getDomainControllers());
        $this->assertEquals(0, $config->getFollowReferrals());
        $this->assertNull($config->getAdminUsername());
        $this->assertNull($config->getAdminPassword());
        $this->assertNull($config->getBaseDn());
        $this->assertFalse($config->getUseSSL());
        $this->assertFalse($config->getUseTLS());
    }

    public function test_dynamic_set_up()
    {
        $settings = [
            'port'               => 500,
            'base_dn'            => 'dc=corp,dc=org',
            'domain_controllers' => ['dc1', 'dc2'],
            'follow_referrals'   => 1,
            'admin_username'     => 'username',
            'admin_password'     => 'password',
            'use_ssl'            => true,
            'use_tls'            => false,
        ];

        $config = new Configuration($settings);

        $this->assertEquals(500, $config->getPort());
        $this->assertEquals('dc=corp,dc=org', $config->getBaseDn());
        $this->assertEquals(['dc1', 'dc2'], $config->getDomainControllers());
        $this->assertEquals('username', $config->getAdminUsername());
        $this->assertEquals('password', $config->getAdminPassword());
        $this->assertTrue($config->getUseSSL());
        $this->assertFalse($config->getUseTLS());
    }

    public function test_set_port()
    {
        $config = new Configuration();

        $config->setPort(500);

        $this->assertEquals('500', $config->getPort());
        $this->assertInternalType('string', $config->getPort());
    }

    public function test_set_base_dn()
    {
        $config = new Configuration();

        $config->setBaseDn('dc=corp');

        $this->assertEquals('dc=corp', $config->getBaseDn());
    }

    public function test_set_domain_controllers()
    {
        $config = new Configuration();

        $controllers = ['dc1', 'dc2'];

        $config->setDomainControllers($controllers);

        $this->assertEquals($controllers, $config->getDomainControllers());
    }

    public function test_set_follow_referrals()
    {
        $config = new Configuration();

        $config->setFollowReferrals(true);

        $this->assertEquals(true, $config->getFollowReferrals());
        $this->assertInternalType('bool', $config->getFollowReferrals());
    }

    public function test_set_admin_username()
    {
        $config = new Configuration();

        $config->setAdminUsername('username');

        $this->assertEquals('username', $config->getAdminUsername());
        $this->assertInternalType('string', $config->getAdminUsername());
    }

    public function test_set_admin_password()
    {
        $config = new Configuration();

        $config->setAdminPassword('password');

        $this->assertEquals('password', $config->getAdminPassword());
        $this->assertInternalType('string', $config->getAdminPassword());
    }

    public function test_set_use_ssl()
    {
        $config = new Configuration();

        $config->setUseSSL(true);

        $this->assertTrue($config->getUseSSL());
    }

    public function test_set_use_ssl_when_using_tls()
    {
        $config = new Configuration();

        $config->setUseTLS(true);

        $this->setExpectedException('Adldap\Exceptions\ConfigurationException');

        $config->setUseSSL(true);
    }

    public function test_set_use_tls()
    {
        $config = new Configuration();

        $config->setUseTLS(true);

        $this->assertTrue($config->getUseTLS());
    }

    public function test_set_use_tls_when_using_ssl()
    {
        $config = new Configuration();

        $config->setUseSSL(true);

        $this->setExpectedException('Adldap\Exceptions\ConfigurationException');

        $config->setUseTLS(true);
    }

    public function test_set_account_suffix()
    {
        $config = new Configuration();

        $config->setAccountSuffix('@corp.org');

        $this->assertEquals('@corp.org', $config->getAccountSuffix());
    }
}
