<?php

namespace Adldap\Tests\Classes;

use Adldap\Search\Factory;
use Adldap\Tests\TestCase;

class FactoryTest extends TestCase
{
    protected function newSearchFactory($connection = null, $dn = 'dc=corp,dc=org', $schema = null)
    {
        if (is_null($connection)) {
            $connection = $this->newConnectionMock();
        }

        return new Factory($connection, $schema, $dn);
    }

    public function test_construct_defaults()
    {
        $search = $this->newSearchFactory();

        $this->assertEquals('', $search->getQuery()->getQuery());
        $this->assertInstanceOf('Adldap\Query\Builder', $search->getQuery());
    }

    public function test_get_and_set_dn()
    {
        $search = $this->newSearchFactory();

        $this->assertEquals('dc=corp,dc=org', $search->getDn());

        $query = $search->setDn(null);

        $this->assertEmpty($query->getDn());
    }

    public function test_new_query()
    {
        $search = $this->newSearchFactory($this->newConnectionMock());

        $new = $search->newQuery();
        $newWithDn = $search->newQuery('testing');

        $this->assertInstanceOf('Adldap\Query\Builder', $new);
        $this->assertEquals('', $new->getDn());

        $this->assertInstanceOf('Adldap\Query\Builder', $newWithDn);
        $this->assertEquals('testing', $newWithDn->getDn());
    }

    public function test_new_grammar()
    {
        $search = $this->newSearchFactory();

        $this->assertInstanceOf('Adldap\Query\Grammar', $search->newGrammar());
    }

    public function test_user_scope()
    {
        $search = $this->newSearchFactory();

        $query = $search->users();

        $this->assertInstanceOf('Adldap\Query\Builder', $query);
        $this->assertCount(2, $query->getWheres());
        $this->assertEquals('(&(objectclass=\70\65\72\73\6f\6e)(objectcategory=\70\65\72\73\6f\6e))', $query->getQuery());
    }

    public function test_printer_scope()
    {
        $search = $this->newSearchFactory();

        $query = $search->printers();

        $this->assertInstanceOf('Adldap\Query\Builder', $query);
        $this->assertCount(1, $query->getWheres());
        $this->assertEquals('(objectclass=\70\72\69\6e\74\71\75\65\75\65)', $query->getQuery());
    }

    public function test_ou_scope()
    {
        $search = $this->newSearchFactory();

        $query = $search->ous();

        $this->assertInstanceOf('Adldap\Query\Builder', $query);
        $this->assertCount(1, $query->getWheres());
        $this->assertEquals('(objectclass=\6f\72\67\61\6e\69\7a\61\74\69\6f\6e\61\6c\75\6e\69\74)', $query->getQuery());
    }

    public function test_group_scope()
    {
        $search = $this->newSearchFactory();

        $query = $search->groups();

        $this->assertInstanceOf('Adldap\Query\Builder', $query);
        $this->assertCount(1, $query->getWheres());
        $this->assertEquals('(objectclass=\67\72\6f\75\70)', $query->getQuery());
    }

    public function test_container_scope()
    {
        $search = $this->newSearchFactory();

        $query = $search->containers();

        $this->assertInstanceOf('Adldap\Query\Builder', $query);
        $this->assertCount(1, $query->getWheres());
        $this->assertEquals('(objectclass=\63\6f\6e\74\61\69\6e\65\72)', $query->getQuery());
    }

    public function test_contact_scope()
    {
        $search = $this->newSearchFactory();

        $query = $search->contacts();

        $this->assertInstanceOf('Adldap\Query\Builder', $query);
        $this->assertCount(1, $query->getWheres());
        $this->assertEquals('(objectclass=\63\6f\6e\74\61\63\74)', $query->getQuery());
    }

    public function test_computer_scope()
    {
        $search = $this->newSearchFactory();

        $query = $search->computers();

        $this->assertInstanceOf('Adldap\Query\Builder', $query);
        $this->assertCount(1, $query->getWheres());
        $this->assertEquals('(objectclass=\63\6f\6d\70\75\74\65\72)', $query->getQuery());
    }
}
