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

    public function testGetQuery()
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
}