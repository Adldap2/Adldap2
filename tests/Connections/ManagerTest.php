<?php

namespace Adldap\Tests\Connections;

use Adldap\Connections\Manager;
use Adldap\Connections\Provider;
use Adldap\Exceptions\AdldapException;
use Adldap\Tests\TestCase;

class ManagerTest extends TestCase
{
    public function test_connection_does_not_exist()
    {
        $manager = new Manager();

        $this->setExpectedException(AdldapException::class);

        $manager->get('missing');
    }

    public function test_add_and_remove()
    {
        $provider = $this->mock(Provider::class);

        $manager = new Manager();

        $manager->add('testing', $provider);

        $this->assertInstanceOf(get_class($provider), $manager->get('testing'));

        $manager->remove('testing');

        $this->setExpectedException(AdldapException::class);

        $manager->get('testing');
    }

    public function test_all_providers()
    {
        $provider1 = $this->mock(Provider::class);
        $provider2 = $this->mock(Provider::class);

        $manager = new Manager();

        $manager->add('provider1', $provider1);
        $manager->add('provider2', $provider2);

        $this->assertCount(2, $manager->all());
    }
}
