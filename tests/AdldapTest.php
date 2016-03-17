<?php

namespace Adldap\Tests;

use Adldap\Adldap;
use Adldap\Connections\Manager;
use Adldap\Connections\Configuration;
use Adldap\Connections\Ldap;
use Adldap\Connections\Provider;
use Adldap\Schemas\Schema;

class AdldapTest extends UnitTestCase
{
    public function test_construct()
    {
        $ad = new Adldap();

        $this->assertInstanceOf(Manager::class, $ad->getManager());
    }

    public function test_construct_with_manager()
    {
        $manager = $this->mock(Manager::class);

        $ad = new Adldap($manager);

        $this->assertInstanceOf(get_class($manager), $ad->getManager());
    }

    public function test_get_provider()
    {
        $manager = new Manager();

        $provider = $this->mock(Provider::class);

        $manager->add('default', $provider);

        $ad = new Adldap($manager);

        $this->assertInstanceOf(get_class($provider), $ad->getProvider('default'));
    }

    public function test_connect()
    {
        $config = $this->mock('Adldap\Connections\Configuration');

        $config->shouldReceive('getUseSSL')->once()->andReturn(false);
        $config->shouldReceive('getUseTLS')->once()->andReturn(false);
        $config->shouldReceive('getDomainControllers')->once()->andReturn(['dc1', 'dc2']);
        $config->shouldReceive('setDomainControllerSelected')->once();
        $config->shouldReceive('getPort')->once()->andReturn(389);
        $config->shouldReceive('getFollowReferrals')->once()->andReturn(1);
        $config->shouldReceive('getAdminUsername')->once()->andReturn('username');
        $config->shouldReceive('getAdminPassword')->once()->andReturn('password');
        $config->shouldReceive('getAdminAccountSuffix')->once()->andReturn('@corp.org');
        $config->shouldReceive('getAccountSuffix')->once()->andReturn('@corp.org');
        $config->shouldReceive('getUseSSO')->once()->andReturn(false);

        $connection = $this->mock('Adldap\Connections\Ldap');

        $connection->shouldReceive('connect')->once()->andReturn(true);
        $connection->shouldReceive('setOption')->twice()->andReturn(true);
        $connection->shouldReceive('bind')->once()->andReturn(true);
        $connection->shouldReceive('isBound')->once()->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $ad = new Adldap();

        $provider = new Provider($config, $connection, Schema::get());

        $ad->getManager()->add('default', $provider);

        $this->assertInstanceOf(Provider::class, $ad->connect('default'));
    }

    public function test_live_connection()
    {
        $config = [
            'account_suffix'     => '@gatech.edu',
            'domain_controllers' => ['whitepages.gatech.edu'],
            'base_dn'            => 'dc=whitepages,dc=gatech,dc=edu',
            'admin_username'     => '',
            'admin_password'     => '',
        ];

        $config = new Configuration($config);

        $provider = new Provider($config, new Ldap(), Schema::get());

        $ad = new Adldap();

        $ad->getManager()->add('default', $provider);

        $connection = $ad->connect('default');

        $this->assertTrue($connection->getConnection()->isBound());
    }
}
