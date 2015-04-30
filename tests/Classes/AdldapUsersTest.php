<?php

namespace Adldap\Tests\Classes;

use Adldap\Tests\FunctionalTestCase;

class AdldapUsersTest extends FunctionalTestCase
{
    /**
     * @var \Mockery\MockInterface
     */
    protected $connectionMock;

    /**
     * @var \Mockery\Mock
     */
    protected $adMock;

    /**
     * @var \Mockery\Mock
     */
    protected $userClassMock;

    public $stubUser = [
        'username' => 'jdoe',
        'firstname' => 'John',
        'surname' => 'Doe',
        'email' => 'jdoe@acme.com',
        'container' => [],
    ];

    public function setUp()
    {
        parent::setUp();

        $this->connectionMock = $this->newConnectionMock();

        $this->adMock = $this->mock('Adldap\Adldap')->makePartial();

        $this->userClassMock = $this->mock('Adldap\Classes\AdldapUsers')->makePartial();

        $this->adMock->setLdapConnection($this->connectionMock);

        $this->userClassMock->__construct($this->adMock);
    }

    public function tearDown()
    {
        parent::tearDown();

        unset($this->connectionMock);
        unset($this->adMock);
        unset($this->userClassMock);
    }

    public function testCreateFailure()
    {
        $attributes = $this->stubUser;

        $attributes['container'] = 'Invalid Container';

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $this->connectionMock->shouldReceive('add')->andReturn(true);
        $this->connectionMock->shouldReceive('close')->andReturn(true);

        $this->userClassMock->create($attributes);
    }

    public function testCreate()
    {
        $attributes = [
            'username' => 'jdoe',
            'firstname' => 'John',
            'surname' => 'Doe',
            'email' => 'jdoe@acme.com',
            'container' => [],
        ];

        $expected = [
            'pwdLastSet' => [-1],
            'displayName' => ['John Doe'],
            'mail' => ['jdoe@acme.com'],
            'givenName' => ['John'],
            'sn' => ['Doe'],
            'cn' => ['John Doe'],
            'sAMAccountname' => ['John Doe'],
            'objectclass' => [
                ['top'],
                ['person'],
                ['organizationalPerson'],
                ['user']
            ],
            'userAccountControl' => [0 => 514]
        ];

        $this->connectionMock->shouldReceive('escape');
        $this->connectionMock->shouldReceive('getEntries');
        $this->connectionMock->shouldReceive('read');
        $this->connectionMock->shouldReceive('add');
        $this->connectionMock->shouldReceive('close');

        $this->userClassMock->create($attributes);
    }
}