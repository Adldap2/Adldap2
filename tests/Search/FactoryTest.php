<?php

namespace Adldap\Tests\Classes;

use Adldap\Schemas\Schema;
use Adldap\Search\Factory;
use Adldap\Tests\UnitTestCase;

class FactoryTest extends UnitTestCase
{
    protected function newSearchFactory($connection = null, $schema = null, $dn = 'dc=corp,dc=org')
    {
        if (is_null($connection)) $connection = $this->newConnectionMock();

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

    public function testNewQuery()
    {
        $search = $this->newSearchFactory($this->newConnectionMock());

        $new = $search->newQuery();
        $newWithDn = $search->newQuery('testing');

        $this->assertInstanceOf('Adldap\Query\Builder', $new);
        $this->assertEquals('', $new->getDn());

        $this->assertInstanceOf('Adldap\Query\Builder', $newWithDn);
        $this->assertEquals('testing', $newWithDn->getDn());
    }

    public function testNewGrammar()
    {
        $search = $this->newSearchFactory();

        $this->assertInstanceOf('Adldap\Query\Grammar', $search->newGrammar());
    }

    public function testUserScope()
    {
        $search = $this->newSearchFactory();

        $query = $search->users();

        $this->assertInstanceOf('Adldap\Query\Builder', $query);
        $this->assertCount(1, $query->getWheres());
        $this->assertEquals('(objectcategory=\70\65\72\73\6f\6e)', $query->getQuery());
    }

    public function testPrinterScope()
    {
        $search = $this->newSearchFactory();

        $query = $search->printers();

        $this->assertInstanceOf('Adldap\Query\Builder', $query);
        $this->assertCount(1, $query->getWheres());
        $this->assertEquals('(objectclass=\70\72\69\6e\74\71\75\65\75\65)', $query->getQuery());
    }

    public function testOuScope()
    {
        $search = $this->newSearchFactory();

        $query = $search->ous();

        $this->assertInstanceOf('Adldap\Query\Builder', $query);
        $this->assertCount(1, $query->getWheres());
        $this->assertEquals('(objectcategory=\6f\72\67\61\6e\69\7a\61\74\69\6f\6e\61\6c\75\6e\69\74)', $query->getQuery());
    }

    public function testGroupScope()
    {
        $search = $this->newSearchFactory();

        $query = $search->groups();

        $this->assertInstanceOf('Adldap\Query\Builder', $query);
        $this->assertCount(1, $query->getWheres());
        $this->assertEquals('(objectcategory=\67\72\6f\75\70)', $query->getQuery());
    }

    public function testContainerScope()
    {
        $search = $this->newSearchFactory();

        $query = $search->containers();

        $this->assertInstanceOf('Adldap\Query\Builder', $query);
        $this->assertCount(1, $query->getWheres());
        $this->assertEquals('(objectcategory=\63\6f\6e\74\61\69\6e\65\72)', $query->getQuery());
    }

    public function testContactScope()
    {
        $search = $this->newSearchFactory();

        $query = $search->contacts();

        $this->assertInstanceOf('Adldap\Query\Builder', $query);
        $this->assertCount(1, $query->getWheres());
        $this->assertEquals('(objectclass=\63\6f\6e\74\61\63\74)', $query->getQuery());
    }

    public function testComputerScope()
    {
        $search = $this->newSearchFactory();

        $query = $search->computers();

        $this->assertInstanceOf('Adldap\Query\Builder', $query);
        $this->assertCount(1, $query->getWheres());
        $this->assertEquals('(objectcategory=\63\6f\6d\70\75\74\65\72)', $query->getQuery());
    }
}
