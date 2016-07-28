<?php

namespace Adldap\Tests\Models;

use Adldap\Models\Factory as ModelFactory;
use Adldap\Schemas\Schema;
use Adldap\Tests\UnitTestCase;

class FactoryTest extends UnitTestCase
{
    protected function newSchema()
    {
        return Schema::get();
    }

    protected function newFactory($builder = null, $schema = null)
    {
        if (is_null($builder)) {
            $builder = $this->newBuilder();
        }

        if (is_null($schema)) {
            $schema = $this->newSchema();
        }

        return new ModelFactory($builder, $schema);
    }

    public function test_construct()
    {
        $factory = $this->newFactory();

        $this->assertInstanceOf('Adldap\Models\Factory', $factory);
    }

    public function test_user()
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
        $this->assertEquals(['John Doe'], $user->getAttribute('cn'));
        $this->assertEquals($class, $user->getAttribute('objectclass'));
    }

    public function test_ou()
    {
        $factory = $this->newFactory();

        $ou = $factory->ou(['ou' => 'Accounting']);

        $class = [
            'top',
            'organizationalunit',
        ];

        $this->assertInstanceOf('Adldap\Models\OrganizationalUnit', $ou);
        $this->assertEquals(['Accounting'], $ou->getAttribute('ou'));
        $this->assertEquals($class, $ou->getAttribute('objectclass'));
    }

    public function test_group()
    {
        $factory = $this->newFactory();

        $group = $factory->group(['cn' => 'Users']);

        $class = [
            'top',
            'group',
        ];

        $this->assertInstanceOf('Adldap\Models\Group', $group);
        $this->assertEquals(['Users'], $group->getAttribute('cn'));
        $this->assertEquals($class, $group->getAttribute('objectclass'));
    }

    public function test_container()
    {
        $factory = $this->newFactory();

        $container = $factory->container(['cn' => 'Container']);

        $this->assertInstanceOf('Adldap\Models\Container', $container);
        $this->assertEquals(['Container'], $container->getAttribute('cn'));
        $this->assertEquals(['organizationalunit'], $container->getAttribute('objectclass'));
    }

    public function test_contact()
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
        $this->assertEquals(['John Doe'], $contact->getAttribute('cn'));
        $this->assertEquals($class, $contact->getAttribute('objectclass'));
    }

    public function test_computer()
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
        $this->assertEquals(['WIN-7'], $computer->getAttribute('cn'));
        $this->assertEquals($class, $computer->getAttribute('objectclass'));
    }
}
