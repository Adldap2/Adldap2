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
        $this->assertEquals([], $config->getPersonFilter());
        $this->assertFalse($config->getUseSSO());
        $this->assertFalse($config->getUseSSL());
        $this->assertFalse($config->getUseTLS());
    }
}
