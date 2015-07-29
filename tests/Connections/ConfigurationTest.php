<?php

namespace Adldap\Tests\Connections;

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

    public function testSetPort()
    {
        $config = new Configuration();

        $config->setPort(500);

        $this->assertEquals('500', $config->getPort());
        $this->assertInternalType('string', $config->getPort());
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

        $config->setFollowReferrals('1');

        $this->assertEquals(1, $config->getFollowReferrals());
        $this->assertInternalType('int', $config->getFollowReferrals());
    }
}
