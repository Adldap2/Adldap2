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

        $ad->addProvider(new DomainConfiguration(), 'first');

        $this->assertInstanceOf(ProviderInterface::class, $ad->getProvider('first'));
    }

    public function test_add_provider_with_configuration_array()
    {
        $ad = new Adldap();

        $ad->addProvider([], 'first');

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

        $this->assertEquals($provider, $ad->getProvider('default'));
    }

    public function test_get_default_provider()
    {
        $ad = new Adldap();

        $provider = new Provider();

        $ad->addProvider($provider, 'new')
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

        $ad->addProvider($provider);

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

    public function test_the_first_provider_is_set_as_default()
    {
        $ad = new Adldap([
            'test1' => [
                'hosts' => ['test1.dc']
            ],
            'test2' => [
                'hosts' => ['test2.dc']
            ],
        ]);

        $provider = $ad->getDefaultProvider();

        $this->assertEquals('test1', $provider->getConnection()->getName());
        $this->assertEquals('test1.dc', $provider->getConfiguration()->get('hosts')[0]);
    }

    public function test_adding_providers_sets_connection_name()
    {
        $ad = new Adldap();

        $ad->addProvider(new DomainConfiguration(), 'domain-a');

        $this->assertEquals('domain-a', $ad->getProvider('domain-a')->getConnection()->getName());
    }
}
