<?php

namespace Adldap\Tests\Connections;

use Adldap\Exceptions\AdldapException;
use Adldap\Connections\Provider;
use Adldap\Connections\Manager;
use Adldap\Tests\UnitTestCase;

class ManagerTest extends UnitTestCase
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
}
