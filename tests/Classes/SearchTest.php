<?php

namespace Adldap\Tests\Classes;

use Adldap\Classes\Search;
use Adldap\Tests\UnitTestCase;

class SearchTest extends UnitTestCase
{
    protected function preparedAdldapMock()
    {
        $configuration = $this->mock('Adldap\Connections\Configuration');

        $configuration->shouldReceive('getBaseDn')->once()->andReturn('dc=corp,dc=org');

        $connection = $this->mock('Adldap\Connections\ConnectionInterface');

        $adldap = $this->mock('Adldap\Adldap');

        $adldap->shouldReceive('getConnection')->once()->andReturn($connection);
        $adldap->shouldReceive('getConfiguration')->once()->andReturn($configuration);

        return $adldap;
    }

    public function testConstructDefaults()
    {
        $search = new Search($this->preparedAdldapMock());

        $this->assertEquals('dc=corp,dc=org', $search->getBaseDn());
        $this->assertEquals('', $search->getQuery());
        $this->assertInstanceOf('Adldap\Adldap', $search->getAdldap());
        $this->assertInstanceOf('Adldap\Query\Builder', $search->getQueryBuilder());
    }

    public function testGetAndSetDn()
    {
        $search = new Search($this->preparedAdldapMock());

        $this->assertEquals('dc=corp,dc=org', $search->getDn());

        $search->setDn(null);

        $this->assertNull($search->getDn());
    }
}
