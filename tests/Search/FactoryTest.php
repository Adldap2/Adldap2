<?php

namespace Adldap\Tests\Classes;

use Adldap\Search\Factory;
use Adldap\Tests\UnitTestCase;

class FactoryTest extends UnitTestCase
{
    public function testConstructDefaults()
    {
        $search = new Factory($this->newConnectionMock(), 'dc=corp,dc=org');

        $this->assertEquals('', $search->getQuery());
        $this->assertInstanceOf('Adldap\Query\Builder', $search->getQueryBuilder());
    }

    public function testGetAndSetDn()
    {
        $search = new Factory($this->newConnectionMock(), 'dc=corp,dc=org');

        $this->assertEquals('dc=corp,dc=org', $search->getDn());

        $search->setDn(null);

        $this->assertNull($search->getDn());
    }
}
