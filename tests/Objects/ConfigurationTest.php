<?php

namespace Adldap\Tests\Objects;

use Adldap\Connections\Configuration;
use Adldap\Tests\FunctionalTestCase;

class ConfigurationTest extends FunctionalTestCase
{
    public function testAllAttributes()
    {
        $configuration = new Configuration();

        $configuration->setBaseDn('Base DN');
        $configuration->setAccountSuffix('Account Suffix');
        $configuration->setAdminUsername('Admin Username');
        $configuration->setAdminPassword('Admin Password');
        $configuration->setDomainControllers(['Domain Controller']);
        $configuration->setFollowReferrals(1);
        $configuration->setPersonFilter(['Filter']);
        $configuration->setPort(500);
        $configuration->setUserIdKey('Person');

        $this->assertEquals('Base DN', $configuration->getBaseDn());
        $this->assertEquals('Account Suffix', $configuration->getAccountSuffix());
        $this->assertEquals('Admin Username', $configuration->getAdminUsername());
        $this->assertEquals('Admin Password', $configuration->getAdminPassword());
        $this->assertEquals(['Domain Controller'], $configuration->getDomainControllers());
        $this->assertEquals(1, $configuration->getFollowReferrals());
        $this->assertEquals('Filter', $configuration->getPersonFilter());
        $this->assertEquals(500, $configuration->getPort());
        $this->assertEquals('Person', $configuration->getUserIdKey());
    }
}
