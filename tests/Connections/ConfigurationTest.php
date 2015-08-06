<?php

namespace Adldap\tests\Connections;

use Adldap\Connections\Configuration;
use Adldap\Tests\UnitTestCase;

class ConfigurationTest extends UnitTestCase
{
    public function testDefaults()
    {
        $config = new Configuration();

        $this->assertEquals(389, $config->getPort());
        $this->assertEquals([], $config->getDomainControllers());
        $this->assertEquals(0, $config->getFollowReferrals());
        $this->assertNull($config->getAdminUsername());
        $this->assertNull($config->getAdminPassword());
        $this->assertNull($config->getBaseDn());
        $this->assertFalse($config->getUseSSO());
        $this->assertFalse($config->getUseSSL());
        $this->assertFalse($config->getUseTLS());
    }

    public function testDynamicSetUp()
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
            'use_sso'            => false,
        ];

        $config = new Configuration($settings);

        $this->assertEquals(500, $config->getPort());
        $this->assertEquals('dc=corp,dc=org', $config->getBaseDn());
        $this->assertEquals(['dc1', 'dc2'], $config->getDomainControllers());
        $this->assertEquals('username', $config->getAdminUsername());
        $this->assertEquals('password', $config->getAdminPassword());
        $this->assertTrue($config->getUseSSL());
        $this->assertFalse($config->getUseSSO());
        $this->assertFalse($config->getUseTLS());
    }

    public function testSetPort()
    {
        $config = new Configuration();

        $config->setPort(500);

        $this->assertEquals('500', $config->getPort());
        $this->assertInternalType('string', $config->getPort());
    }

    public function testSetBaseDn()
    {
        $config = new Configuration();

        $config->setBaseDn('dc=corp');

        $this->assertEquals('dc=corp', $config->getBaseDn());
    }

    public function testSetDomainControllers()
    {
        $config = new Configuration();

        $controllers = ['dc1', 'dc2'];

        $config->setDomainControllers($controllers);

        $this->assertEquals($controllers, $config->getDomainControllers());
    }

    public function testSetDomainControllersInvalidType()
    {
        $config = new Configuration();

        try {
            $config->setDomainControllers('Invalid Type');

            $passes = false;
        } catch (\Exception $e) {
            $passes = true;
        }

        $this->assertTrue($passes);
    }

    public function testSetFollowReferrals()
    {
        $config = new Configuration();

        $config->setFollowReferrals(true);

        $this->assertEquals(true, $config->getFollowReferrals());
        $this->assertInternalType('bool', $config->getFollowReferrals());
    }

    public function testSetAdminUsername()
    {
        $config = new Configuration();

        $config->setAdminUsername('username');

        $this->assertEquals('username', $config->getAdminUsername());
        $this->assertInternalType('string', $config->getAdminUsername());
    }

    public function testSetAdminPassword()
    {
        $config = new Configuration();

        $config->setAdminPassword('password');

        $this->assertEquals('password', $config->getAdminPassword());
        $this->assertInternalType('string', $config->getAdminPassword());
    }

    public function testSetUseSSL()
    {
        $config = new Configuration();

        $config->setUseSSL(true);

        $this->assertTrue($config->getUseSSL());
    }

    public function testSetUseSSLWhenUsingTLS()
    {
        $config = new Configuration();

        $config->setUseTLS(true);

        $this->setExpectedException('Adldap\Exceptions\ConfigurationException');

        $config->setUseSSL(true);
    }

    public function testSetUseTLS()
    {
        $config = new Configuration();

        $config->setUseTLS(true);

        $this->assertTrue($config->getUseTLS());
    }

    public function testSetUseTLSWhenUsingSSL()
    {
        $config = new Configuration();

        $config->setUseSSL(true);

        $this->setExpectedException('Adldap\Exceptions\ConfigurationException');

        $config->setUseTLS(true);
    }

    public function testSetUseSSO()
    {
        $config = new Configuration();

        $config->setUseSSO(true);

        $this->assertTrue($config->getUseSSO());
    }

    public function testSetAccountSuffix()
    {
        $config = new Configuration();

        $config->setAccountSuffix('@corp.org');

        $this->assertEquals('@corp.org', $config->getAccountSuffix());
    }
}
