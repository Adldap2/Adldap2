<?php

namespace Adldap\Tests;

use Adldap\Adldap;
use Adldap\Classes\AdldapSearch;

class AdldapSearchTest extends AdldapBaseTest
{
    protected function newAdldap($connection = null)
    {
        return new Adldap(array(), $connection, false);
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

        $expected = array(
            array(
                'field' => 'field',
                'operator' => '=',
                'value' => 'value',
            )
        );

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

        $expected = array(
            array(
                'field' => 'field',
                'operator' => '=',
                'value' => 'value',
            )
        );

        $this->assertEquals($expected, $search->getOrWheres());
    }

    public function testSearchGetSelects()
    {
        $connection = $this->newConnectionMock();

        $ad = $this->newAdldap($connection);

        $search = new AdldapSearch($ad);

        $connection->shouldReceive('close')->once()->andReturn(true);

        $selects = array(
            'cn',
            'dn'
        );

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

        $results = array(
            'count' => 1,
            array(
                'cn' => array(
                    'John Doe'
                )
            )
        );

        $connection
            ->shouldReceive('escape')->once()->andReturn('*')
            ->shouldReceive('search')->once()->andReturn('resource')
            ->shouldReceive('getEntries')->once()->andReturn($results)
            ->shouldReceive('close')->once()->andReturn(true);

        $expected = array(
            0 => array(
                'cn' => 'John Doe'
            )
        );

        $this->assertEquals($expected, $search->all());
    }

    public function testSearchWhere()
    {
        $connection = $this->newConnectionMock();

        $ad = $this->newAdldap($connection);

        $search = new AdldapSearch($ad);

        $ldapResults = array(
            'count' => 1,
            array(
                'cn' => array(
                    'John Doe'
                )
            )
        );

        $connection
            ->shouldReceive('escape')->once()->andReturn('*')
            ->shouldReceive('search')->once()->andReturn('resource')
            ->shouldReceive('getEntries')->once()->andReturn($ldapResults)
            ->shouldReceive('close')->once()->andReturn(true);

        $expected = array(
            0 => array(
                'cn' => 'John Doe'
            )
        );

        $searchResults = $search->where('cn', '=', 'John Doe')->get();

        $this->assertEquals($expected, $searchResults);
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