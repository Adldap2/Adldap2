<?php

namespace Adldap\Tests\Models;

use Adldap\Schemas\Schema;
use Adldap\Models\Factory as ModelFactory;
use Adldap\Tests\UnitTestCase;

class FactoryTest extends UnitTestCase
{
    protected function newSchema()
    {
        return Schema::get();
    }

    protected function newFactory($builder = null, $schema = null)
    {
        if (is_null($builder)) $builder = $this->newBuilder();

        if (is_null($schema)) $schema = $this->newSchema();

        return new ModelFactory($builder, $schema);
    }

    public function testConstruct()
    {
        $factory = $this->newFactory();

        $this->assertInstanceOf('Adldap\Models\Factory', $factory);
    }

    public function testUser()
    {
        $factory = $this->newFactory();

        $user = $factory->user(['cn' => 'John Doe']);

        $class = [
            'top',
            'person',
            'organizationalperson',
            'user',
        ];

        $this->assertInstanceOf('Adldap\Models\User', $user);
        $this->assertEquals('John Doe', $user->getAttribute('cn'));
        $this->assertEquals($class, $user->getAttribute('objectclass'));
    }

    public function testOu()
    {
        $factory = $this->newFactory();

        $ou = $factory->ou(['ou' => 'Accounting']);

        $class = [
            'top',
            'organizationalunit',
        ];

        $this->assertInstanceOf('Adldap\Models\OrganizationalUnit', $ou);
        $this->assertEquals('Accounting', $ou->getAttribute('ou'));
        $this->assertEquals($class, $ou->getAttribute('objectclass'));
    }

    public function testGroup()
    {
        $factory = $this->newFactory();

        $group = $factory->group(['cn' => 'Users']);

        $class = [
            'top',
            'group',
        ];

        $this->assertInstanceOf('Adldap\Models\Group', $group);
        $this->assertEquals('Users', $group->getAttribute('cn'));
        $this->assertEquals($class, $group->getAttribute('objectclass'));
    }

    public function testContainer()
    {
        $factory = $this->newFactory();

        $container = $factory->container(['cn' => 'Container']);

        $class = 'organizationalunit';

        $this->assertInstanceOf('Adldap\Models\Container', $container);
        $this->assertEquals('Container', $container->getAttribute('cn'));
        $this->assertEquals($class, $container->getAttribute('objectclass'));
    }

    public function testContact()
    {
        $factory = $this->newFactory();

        $contact = $factory->contact(['cn' => 'John Doe']);

        $class = [
            'top',
            'person',
            'organizationalperson',
            'contact',
        ];

        $this->assertInstanceOf('Adldap\Models\User', $contact);
        $this->assertEquals('John Doe', $contact->getAttribute('cn'));
        $this->assertEquals($class, $contact->getAttribute('objectclass'));
    }

    public function testComputer()
    {
        $factory = $this->newFactory();

        $computer = $factory->computer(['cn' => 'WIN-7']);

        $class = [
            'top',
            'person',
            'organizationalperson',
            'user',
            'computer',
        ];

        $this->assertInstanceOf('Adldap\Models\Computer', $computer);
        $this->assertEquals('WIN-7', $computer->getAttribute('cn'));
        $this->assertEquals($class, $computer->getAttribute('objectclass'));
    }
}
