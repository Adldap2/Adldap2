<?php

namespace Adldap\Tests\Objects;

use Adldap\Objects\User;
use Adldap\Tests\FunctionalTestCase;

class UserObjectTest extends FunctionalTestCase
{
    protected function stubbedUserAttributes()
    {
        return array(
            'display_name' => 'John Doe',
            'username' => 'jdoe',
            'firstname' => 'John',
            'surname' => 'Doe',
            'email' => 'jdoe@email.com',
            'container' => array('Container Parent', 'Container Child'),
            'password' => 'New Password',
        );
    }

    public function testUserConstruct()
    {
        $user = new User($this->stubbedUserAttributes());

        $this->assertEquals('jdoe', $user->username);
        $this->assertEquals('John', $user->firstname);
        $this->assertEquals('Doe', $user->surname);
        $this->assertEquals('jdoe@email.com', $user->email);
        $this->assertEquals(array('Container Parent', 'Container Child'), $user->container);
        $this->assertEquals('New Password', $user->password);
    }

    public function testUserToSchema()
    {
        $attributes = $this->stubbedUserAttributes();

        $user = new User($attributes);

        $this->assertEquals($attributes, $user->toSchema());
    }

    public function testUserToSchemaDisplayNameAutoSet()
    {
        $attributes = $this->stubbedUserAttributes();

        unset($attributes['display_name']);

        $user = new User($attributes);

        $schema = $user->toSchema();

        $this->assertEquals('John Doe', $schema['display_name']);
    }

    public function testUserToSchemaContainerFailure()
    {
        $attributes = $this->stubbedUserAttributes();

        $attributes['container'] = 'Invalid Container';

        $user = new User($attributes);

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $user->toSchema();
    }

    public function testUserToSchemaUsernameFailure()
    {
        $attributes = $this->stubbedUserAttributes();

        unset($attributes['username']);

        $user = new User($attributes);

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $user->toSchema();
    }

    public function testUserToSchemaFirstnameFailure()
    {
        $attributes = $this->stubbedUserAttributes();

        unset($attributes['firstname']);

        $user = new User($attributes);

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $user->toSchema();
    }

    public function testUserToSchemaSurnameFailure()
    {
        $attributes = $this->stubbedUserAttributes();

        unset($attributes['surname']);

        $user = new User($attributes);

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $user->toSchema();
    }

    public function testUserToSchemaEmailFailure()
    {
        $attributes = $this->stubbedUserAttributes();

        unset($attributes['email']);

        $user = new User($attributes);

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $user->toSchema();
    }
}