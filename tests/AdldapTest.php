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

    public function testSetConnection()
    {
        $connection = new Ldap();

        $ad = new Adldap([], null, $autoConnect = false);

        $ad->setConnection($connection);

        $this->assertInstanceOf(get_class($connection), $ad->getConnection());
    }

    public function testGetConfiguration()
    {
        $config = new Configuration();

        $ad = new Adldap($config, new Ldap(), $autoConnect = false);

        $this->assertInstanceOf(get_class($config), $ad->getConfiguration());
    }

    public function testSetConfiguration()
    {
        $ad = new Adldap([], new Ldap(), $autoConnect = false);

        $config = new Configuration(['admin_username' => 'username']);

        $ad->setConfiguration($config);

        $this->assertInstanceOf(get_class($config), $ad->getConfiguration());
        $this->assertEquals('username', $ad->getConfiguration()->getAdminUsername());
    }

    public function testConnect()
    {
        $ad = new Adldap([], new Ldap(), $autoConnect = false);

        $config = $this->mock('Adldap\Connections\Configuration');

        $config->shouldReceive('getUseSSL')->once()->andReturn(false);
        $config->shouldReceive('getUseTLS')->once()->andReturn(false);
        $config->shouldReceive('getDomainControllers')->once()->andReturn(['dc1', 'dc2']);
        $config->shouldReceive('setDomainControllerSelected')->once();
        $config->shouldReceive('getPort')->once()->andReturn(389);
        $config->shouldReceive('getFollowReferrals')->once()->andReturn(1);
        $config->shouldReceive('getAdminUsername')->once()->andReturn('username');
        $config->shouldReceive('getAdminPassword')->once()->andReturn('password');
        $config->shouldReceive('getAccountSuffix')->once()->andReturn('@corp.org');
        $config->shouldReceive('getUseSSO')->once()->andReturn(false);

        $connection = $this->mock('Adldap\Connections\Ldap');

        $connection->shouldReceive('connect')->once()->andReturn(true);
        $connection->shouldReceive('setOption')->twice()->andReturn(true);
        $connection->shouldReceive('bind')->once()->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $ad->setConfiguration($config);

        $ad->setConnection($connection);

        $this->assertNull($ad->connect());
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

    public function testLiveConnection()
    {
        $config = [
            'account_suffix'     => '@gatech.edu',
            'domain_controllers' => ['whitepages.gatech.edu'],
            'base_dn'            => 'dc=whitepages,dc=gatech,dc=edu',
            'admin_username'     => '',
            'admin_password'     => '',
        ];

        $ad = new \Adldap\Adldap($config);

        $ad->connect();

        $this->assertTrue($ad->getConnection()->isBound());
    }
}
