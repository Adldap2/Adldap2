<?php

namespace Adldap\Tests;

use Adldap\Adldap;
use Adldap\Connections\Ldap;
use Adldap\Connections\Provider;
use Adldap\Configuration\DomainConfiguration;
use Adldap\Connections\ProviderInterface;

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

    public function test_add_provider_with_configuration_instance()
    {
        $ad = new Adldap();

        $ad->addProvider('first', new DomainConfiguration());

        $this->assertInstanceOf(ProviderInterface::class, $ad->getProvider('first'));
    }

    public function test_add_provider_with_configuration_array()
    {
        $ad = new Adldap();

        $ad->addProvider('first', []);

        $this->assertInstanceOf(ProviderInterface::class, $ad->getProvider('first'));
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

    public function test_connect()
    {
        $connection = $this->mock(Ldap::class);

        $connection->shouldReceive('connect')->once()->andReturn(true)
            ->shouldReceive('setOptions')->once()
            ->shouldReceive('bind')->once()->andReturn(true)
            ->shouldReceive('isBound')->once()->andReturn(true)
            ->shouldReceive('close')->once()->andReturn(true);

        $ad = new Adldap();

        $provider = new Provider([], $connection);

        $ad->addProvider('default', $provider);

        $this->assertInstanceOf(Provider::class, $ad->connect('default'));
    }

    /**
     * @expectedException \Adldap\AdldapException
     */
    public function test_invalid_default_provider()
    {
        $ad = new Adldap();

        $ad->getDefaultProvider();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_invalid_provider()
    {
        $ad = new Adldap();

        $ad->addProvider('first', 'invalid');
    }
}
