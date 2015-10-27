<?php

namespace Adldap\tests\Classes;

use Adldap\Classes\Search;
use Adldap\Tests\UnitTestCase;

class SearchTest extends UnitTestCase
{
    protected function preparedManagerMock()
    {
        $configuration = $this->mock('Adldap\Connections\Configuration');

        $configuration->shouldReceive('getBaseDn')->once()->andReturn('dc=corp,dc=org');

        $connection = $this->mock('Adldap\Connections\ConnectionInterface');
        $connection->shouldReceive('isBound')->once()->andReturn(true);
        $connection->shouldReceive('close')->once()->andReturn(true);

        $m = $this->mock('Adldap\Connections\Manager');

        $m->shouldReceive('getConnection')->once()->andReturn($connection);
        $m->shouldReceive('getConfiguration')->once()->andReturn($configuration);

        return $m;
    }

    public function testConstructDefaults()
    {
        $search = new Search($this->preparedManagerMock());

        $this->assertEquals('', $search->getQuery());
        $this->assertInstanceOf('Adldap\Connections\Manager', $search->getManager());
        $this->assertInstanceOf('Adldap\Query\Builder', $search->getQueryBuilder());
    }

    public function testGetAndSetDn()
    {
        $search = new Search($this->preparedManagerMock());

        $this->assertEquals('dc=corp,dc=org', $search->getDn());

        $search->setDn(null);

        $this->assertNull($search->getDn());
    }
}
