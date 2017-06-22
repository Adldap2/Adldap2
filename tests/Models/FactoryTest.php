<?php

namespace Adldap\Tests\Models;

use Adldap\Models\User;
use Adldap\Models\Entry;
use Adldap\Models\Group;
use Adldap\Models\Contact;
use Adldap\Models\Computer;
use Adldap\Models\Container;
use Adldap\Models\OrganizationalUnit;
use Adldap\Models\Factory as ModelFactory;
use Adldap\Tests\TestCase;

class FactoryTest extends TestCase
{
    protected function newFactory($builder = null, $schema = null)
    {
        if (is_null($builder)) {
            $builder = $this->newBuilder();
        }

        return new ModelFactory($builder, $schema);
    }

    public function test_construct()
    {
        $factory = $this->newFactory();

        $this->assertInstanceOf(ModelFactory::class, $factory);
    }

    public function test_entry()
    {
        $factory = $this->newFactory();

        $entry = $factory->entry(['cn' => 'John Doe']);

        $this->assertInstanceOf(Entry::class, $entry);
        $this->assertEquals(['John Doe'], $entry->getAttribute('cn'));
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

        $this->assertInstanceOf(User::class, $user);
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

        $this->assertInstanceOf(OrganizationalUnit::class, $ou);
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

        $this->assertInstanceOf(Group::class, $group);
        $this->assertEquals(['Users'], $group->getAttribute('cn'));
        $this->assertEquals($class, $group->getAttribute('objectclass'));
    }

    public function test_container()
    {
        $factory = $this->newFactory();

        $container = $factory->container(['cn' => 'Container']);

        $this->assertInstanceOf(Container::class, $container);
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

        $this->assertInstanceOf(Contact::class, $contact);
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

        $this->assertInstanceOf(Computer::class, $computer);
        $this->assertEquals(['WIN-7'], $computer->getAttribute('cn'));
        $this->assertEquals($class, $computer->getAttribute('objectclass'));
    }
}
