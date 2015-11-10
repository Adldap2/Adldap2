<?php

namespace Adldap\Tests\Classes;

use Adldap\Schemas\Schema;
use Adldap\Search\Factory;
use Adldap\Tests\UnitTestCase;

class FactoryTest extends UnitTestCase
{
    protected function newSearchFactory($connection, $schema = null, $dn = 'dc=corp,dc=org')
    {
        if (is_null($schema)) $schema = Schema::get();

        return new Factory($connection, $schema, $dn);
    }

    public function testConstructDefaults()
    {
        $search = $this->newSearchFactory($this->newConnectionMock(), Schema::get(), 'dc=corp,dc=org');

        $this->assertEquals('', $search->getQuery());
        $this->assertInstanceOf('Adldap\Query\Builder', $search->getQueryBuilder());
    }

    public function testGetAndSetDn()
    {
        $search = $this->newSearchFactory($this->newConnectionMock(), Schema::get(), 'dc=corp,dc=org');

        $this->assertEquals('dc=corp,dc=org', $search->getDn());

        $search->setDn(null);

        $this->assertNull($search->getDn());
    }
}
