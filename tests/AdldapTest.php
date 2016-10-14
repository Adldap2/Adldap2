<?php

namespace Adldap\Tests;

use Adldap\Adldap;
use Adldap\Connections\Ldap;
use Adldap\Connections\Provider;
use Adldap\AdldapException;

class AdldapTest extends TestCase
{
    public function test_construct()
    {
        $providers = [
            'first' => new Provider(),
            'second' => new Provider(),
        ];

        $ad = new Adldap($providers);

        $this->assertEquals($providers['first'], $ad->getProvider('first'));
        $this->assertEquals($providers['second'], $ad->getProvider('second'));
    }

    public function test_get_providers()
    {
        $providers = [
            'first' => new Provider(),
            'second' => new Provider(),
        ];

        $ad = new Adldap($providers);

        $this->assertEquals($providers, $ad->getProviders());
    }

    public function test_get_provider()
    {
        $provider = $this->mock(Provider::class);

        $ad = new Adldap([
            'default' => $provider,
        ]);

        $this->assertInstanceOf(get_class($provider), $ad->getProvider('default'));
    }

    public function test_get_default_provider()
    {
        $ad = new Adldap();

        $provider = new Provider();

        $ad->addProvider('new', $provider)
            ->setDefaultProvider('new');

        $this->assertInstanceOf(Provider::class, $ad->getDefaultProvider());
    }

    public function test_invalid_default_provider()
    {
        $ad = new Adldap();

        $this->setExpectedException(AdldapException::class);

        $ad->getDefaultProvider();
    }

    public function test_connect()
    {
        $connection = $this->mock(Ldap::class);

        $connection->shouldReceive('connect')->once()->andReturn(true)
            ->shouldReceive('setOption')->twice()->andReturn(true)
            ->shouldReceive('bind')->once()->andReturn(true)
            ->shouldReceive('isBound')->once()->andReturn(true)
            ->shouldReceive('close')->once()->andReturn(true);

        $ad = new Adldap();

        $provider = new Provider([], $connection);

        $ad->addProvider('default', $provider);

        $this->assertInstanceOf(Provider::class, $ad->connect('default'));
    }
}
