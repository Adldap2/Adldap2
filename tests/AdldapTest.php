<?php

namespace Adldap\Tests;

use Adldap\Adldap;
use Adldap\Connections\Manager;
use Adldap\Connections\Provider;
use Adldap\Exceptions\AdldapException;
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

        $config->shouldReceive('getUseSSL')->once()->andReturn(false)
            ->shouldReceive('getUseTLS')->once()->andReturn(false)
            ->shouldReceive('getDomainControllers')->once()->andReturn(['dc1', 'dc2'])
            ->shouldReceive('setDomainControllerSelected')->once()
            ->shouldReceive('getPort')->once()->andReturn(389)
            ->shouldReceive('getTimeout')->once()->andReturn(5)
            ->shouldReceive('getFollowReferrals')->once()->andReturn(1)
            ->shouldReceive('getAdminCredentials')->once()->andReturn(['username', 'password', 'suffix'])
            ->shouldReceive('getAccountSuffix')->once()->andReturn('@corp.org')
            ->shouldReceive('getUseSSO')->once()->andReturn(false);

        $connection = $this->mock('Adldap\Connections\Ldap');

        $connection->shouldReceive('connect')->once()->andReturn(true)
            ->shouldReceive('setOption')->twice()->andReturn(true)
            ->shouldReceive('bind')->once()->andReturn(true)
            ->shouldReceive('isBound')->once()->andReturn(true)
            ->shouldReceive('close')->once()->andReturn(true);

        $ad = new Adldap();

        $provider = new Provider($config, $connection, Schema::get());

        $ad->getManager()->add('default', $provider);

        $this->assertInstanceOf(Provider::class, $ad->connect('default'));
    }

    public function test_default_provider()
    {
        $config = $this->mock('Adldap\Connections\Configuration');

        $config->shouldReceive('getUseSSL')->andReturn(false)
            ->shouldReceive('getUseTLS')->andReturn(false)
            ->shouldReceive('getFollowReferrals')->andReturn(false)
            ->shouldReceive('getDomainControllers')->andReturn([])
            ->shouldReceive('getPort')->andReturn(387)
            ->shouldReceive('getTimeout')->once()->andReturn(5);

        $connection = $this->mock('Adldap\Connections\Ldap');

        $connection->shouldReceive('setOption')->twice()
            ->shouldReceive('connect')->once()
            ->shouldReceive('isBound')->once()->andReturn(false);

        $ad = new Adldap();

        $provider = new Provider($config, $connection, Schema::get());

        $ad->getManager()
            ->add('new', $provider)
            ->setDefault('new');

        $this->assertInstanceOf(Provider::class, $ad->getDefaultProvider());
    }

    public function test_invalid_default_provider()
    {
        $ad = new Adldap();

        $this->setExpectedException(AdldapException::class);

        $ad->getDefaultProvider();
    }
}
