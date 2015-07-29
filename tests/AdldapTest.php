<?php

namespace Adldap\Tests;

use Adldap\Adldap;
use Adldap\Connections\Configuration;
use Adldap\Connections\Ldap;

class AdldapTest extends UnitTestCase
{
    public function testConstruct()
    {
        $config = $this->mock('Adldap\Connections\Configuration');

        $config->shouldReceive('getUseSSL')->once()->andReturn(false);
        $config->shouldReceive('getUseTLS')->once()->andReturn(false);
        $config->shouldReceive('getUseSSO')->once()->andReturn(false);
        $config->shouldReceive('getDomainControllers')->once()->andReturn([]);
        $config->shouldReceive('getPort')->once()->andReturn(389);
        $config->shouldReceive('getFollowReferrals')->once()->andReturn(1);
        $config->shouldReceive('getAdminUsername')->once()->andReturn('admin');
        $config->shouldReceive('getAdminPassword')->once()->andReturn('password');
        $config->shouldReceive('getAccountSuffix')->once()->andReturn('@corp');

        $ad = new Adldap($config, null, $autoConnect = false);

        $this->assertInstanceOf('Adldap\Connections\Configuration', $ad->getConfiguration());
    }

    public function testGetConnection()
    {
        $connection = new Ldap();

        $ad = new Adldap([], $connection, $autoConnect = false);

        $this->assertInstanceOf(get_class($connection), $ad->getConnection());
    }

    public function testGetConfiguration()
    {
        $config = new Configuration();

        $ad = new Adldap($config, new Ldap(), $autoConnect = false);

        $this->assertInstanceOf(get_class($config), $ad->getConfiguration());
    }

    public function testGroups()
    {
        $ad = new Adldap([], new Ldap(), $autoConnect = false);

        $this->assertInstanceOf('Adldap\Classes\Groups', $ad->groups());
    }

    public function testUsers()
    {
        $ad = new Adldap([], new Ldap(), $autoConnect = false);

        $this->assertInstanceOf('Adldap\Classes\Users', $ad->users());
    }

    public function testContainers()
    {
        $ad = new Adldap([], new Ldap(), $autoConnect = false);

        $this->assertInstanceOf('Adldap\Classes\Containers', $ad->containers());
    }

    public function testContacts()
    {
        $ad = new Adldap([], new Ldap(), $autoConnect = false);

        $this->assertInstanceOf('Adldap\Classes\Contacts', $ad->contacts());
    }

    public function testExchange()
    {
        $ad = new Adldap([], new Ldap(), $autoConnect = false);

        $this->assertInstanceOf('Adldap\Classes\Exchange', $ad->exchange());
    }

    public function testComputers()
    {
        $ad = new Adldap([], new Ldap(), $autoConnect = false);

        $this->assertInstanceOf('Adldap\Classes\Computers', $ad->computers());
    }

    public function testSearch()
    {
        $ad = new Adldap([], new Ldap(), $autoConnect = false);

        $this->assertInstanceOf('Adldap\Classes\Search', $ad->search());
    }
}
