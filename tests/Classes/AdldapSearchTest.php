<?php

namespace Adldap\Tests\Classes;

use Adldap\Adldap;
use Adldap\Classes\AdldapSearch;
use Adldap\Tests\AdldapBaseTest;

class AdldapSearchTest extends AdldapBaseTest
{
    protected function newAdldap($connection = null)
    {
        return new Adldap([], $connection, false);
    }

    public function testSearchGetQuery()
    {
        $connection = $this->newConnectionMock();

        $ad = $this->newAdldap($connection);

        $search = new AdldapSearch($ad);

        $connection
            ->shouldReceive('escape')->once()->andReturn('*')
            ->shouldReceive('close')->once()->andReturn(true);

        $query = $search->where('cn', '*')->getQuery();

        $this->assertEquals('(cn=*)', $query);
    }

    public function testSearchGetWheres()
    {
        $connection = $this->newConnectionMock();

        $ad = $this->newAdldap($connection);

        $search = new AdldapSearch($ad);

        $connection
            ->shouldReceive('escape')->once()->andReturn('value')
            ->shouldReceive('close')->once()->andReturn(true);

        $search->where('field', '=', 'value');

        $expected = [
            [
                'field' => 'field',
                'operator' => '=',
                'value' => 'value',
            ]
        ];

        $this->assertEquals($expected, $search->getWheres());
    }

    public function testSearchGetOrWheres()
    {
        $connection = $this->newConnectionMock();

        $ad = $this->newAdldap($connection);

        $search = new AdldapSearch($ad);

        $connection
            ->shouldReceive('escape')->once()->andReturn('value')
            ->shouldReceive('close')->once()->andReturn(true);

        $search->orWhere('field', '=', 'value');

        $expected = [
            [
                'field' => 'field',
                'operator' => '=',
                'value' => 'value',
            ]
        ];

        $this->assertEquals($expected, $search->getOrWheres());
    }

    public function testSearchGetSelects()
    {
        $connection = $this->newConnectionMock();

        $ad = $this->newAdldap($connection);

        $search = new AdldapSearch($ad);

        $connection->shouldReceive('close')->once()->andReturn(true);

        $selects = [
            'cn',
            'dn'
        ];

        $search->select($selects);

        $this->assertEquals($selects, $search->getSelects());
    }

    public function testSearchHasSelects()
    {
        $connection = $this->newConnectionMock();

        $ad = $this->newAdldap($connection);

        $search = new AdldapSearch($ad);

        $connection->shouldReceive('close')->once()->andReturn(true);

        $search->select('cn');

        $this->assertTrue($search->hasSelects());
    }

    public function testSearchAll()
    {
        $connection = $this->newConnectionMock();

        $ad = $this->newAdldap($connection);

        $search = new AdldapSearch($ad);

        $results = [
            'count' => 1,
            0 => [
                'count' => 1,
                'cn' => [
                    'John Doe'
                ]
            ]
        ];

        $connection
            ->shouldReceive('escape')->twice()->andReturn('*')
            ->shouldReceive('read')->once()->andReturn('DN')
            ->shouldReceive('search')->once()->andReturn('resource')
            ->shouldReceive('getEntries')->twice()->andReturn($results)
            ->shouldReceive('close')->once()->andReturn(true);

        $expected = [
            0 => [
                'cn' => 'John Doe'
            ]
        ];

        $this->assertEquals($expected, $search->all());
    }

    public function testSearchWhere()
    {
        $connection = $this->newConnectionMock();

        $ad = $this->newAdldap($connection);

        $search = new AdldapSearch($ad);

        $ldapResults = [
            'count' => 1,
            [
                'count' => 1,
                'cn' => [
                    'John Doe'
                ]
            ]
        ];

        $connection
            ->shouldReceive('escape')->twice()->andReturn('DN')
            ->shouldReceive('read')->once()->andReturn('DN')
            ->shouldReceive('search')->once()->andReturn('resource')
            ->shouldReceive('getEntries')->twice()->andReturn($ldapResults)
            ->shouldReceive('close')->once()->andReturn(true);

        $expected = [
            0 => [
                'cn' => 'John Doe'
            ]
        ];

        $searchResults = $search->where('cn', '=', 'John Doe')->get();

        $this->assertEquals($expected, $searchResults);
    }

    public function testSearchQuery()
    {
        $connection = $this->newConnectionMock();

        $ad = $this->newAdldap($connection);

        $search = new AdldapSearch($ad);

        $connection
            ->shouldReceive('escape')->once()->andReturn('DN')
            ->shouldReceive('read')->once()
            ->shouldReceive('search')->once()->andReturn('resource')
            ->shouldReceive('getEntries')->once()->andReturn([])
            ->shouldReceive('close')->once()->andReturn(true);

        $this->assertEquals([], $search->query('(cn=test)'));
    }

    public function testSearchQueryNonRecursive()
    {
        $connection = $this->newConnectionMock();

        $ad = $this->newAdldap($connection);

        $search = new AdldapSearch($ad);

        $search->recursive(false);
        
        $connection
            ->shouldReceive('escape')->once()->andReturn('DN')
            ->shouldReceive('read')->once()
            ->shouldReceive('listing')->once()->andReturn('resource')
            ->shouldReceive('getEntries')->once()->andReturn([])
            ->shouldReceive('close')->once()->andReturn(true);

        $this->assertEquals([], $search->query('(cn=test)'));
    }

    public function testSearchWhereInvalidOperator()
    {
        $connection = $this->newConnectionMock();

        $ad = $this->newAdldap($connection);

        $search = new AdldapSearch($ad);

        $connection->shouldReceive('close')->once()->andReturn(true);

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $search->where('field', 'invalid operator', 'value');
    }

    public function testSearchOrWhereInvalidOperator()
    {
        $connection = $this->newConnectionMock();

        $ad = $this->newAdldap($connection);

        $search = new AdldapSearch($ad);

        $connection->shouldReceive('close')->once()->andReturn(true);

        $this->setExpectedException('Adldap\Exceptions\AdldapException');

        $search->orWhere('field', 'invalid operator', 'value');
    }
}