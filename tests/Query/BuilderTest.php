<?php

namespace Adldap\Tests\Query;

use Adldap\Query\Bindings\Select;
use Adldap\Query\Bindings\Filter;
use Adldap\Query\Bindings\OrWhere;
use Adldap\Query\Bindings\Where;
use Adldap\Schemas\Schema;
use Adldap\Query\Builder;
use Adldap\Query\Grammar;
use Adldap\Tests\UnitTestCase;

class BuilderTest extends UnitTestCase
{
    protected function newBuilder($connection = null)
    {
        if(is_null($connection)) {
            $connection = $this->newConnectionMock();
        }

        return new Builder($connection, new Grammar(), Schema::get());
    }

    protected function newConnectionMock()
    {
        return $this->mock('Adldap\Contracts\Connections\ConnectionInterface');
    }

    public function test_construct()
    {
        $b = $this->newBuilder();

        $this->assertEmpty($b->getQuery());
    }

    public function test_select_array()
    {
        $b = $this->newBuilder();

        $b->select(['testing']);

        $expected = [
            'testing',
            'objectcategory',
            'objectclass',
            'distinguishedname',
        ];

        $this->assertEquals($expected, $b->getSelects());
    }

    public function test_select_string()
    {
        $b = $this->newBuilder();

        $b->select('testing');

        $expected = [
            'testing',
            'objectcategory',
            'objectclass',
            'distinguishedname',
        ];

        $this->assertEquals($expected, $b->getSelects());
    }

    public function test_select_empty_string()
    {
        $b = $this->newBuilder();

        $b->select('');

        $expected = [
            '',
            'objectcategory',
            'objectclass',
            'distinguishedname'
        ];

        $this->assertEquals($expected, $b->getSelects());
    }

    public function test_has_selects()
    {
        $b = $this->newBuilder();

        $b->select('test');

        $this->assertTrue($b->hasSelects());
    }

    public function test_where()
    {
        $b = $this->newBuilder();

        $b->where('cn', '=', 'test');

        $where = $b->getWheres()[0];

        $this->assertEquals('cn', $where->getField());
        $this->assertEquals('=', $where->getOperator());
        $this->assertEquals('\74\65\73\74', $where->getValue());
    }

    public function test_where_with_array()
    {
        $b = $this->newBuilder();

        $b->where([
            'cn'    => 'test',
            'name'  => 'test',
        ]);

        $whereOne = $b->getWheres()[0];

        $this->assertEquals('cn', $whereOne->getField());
        $this->assertEquals('=', $whereOne->getOperator());
        $this->assertEquals('\74\65\73\74', $whereOne->getValue());

        $whereTwo = $b->getWheres()[1];

        $this->assertEquals('name', $whereTwo->getField());
        $this->assertEquals('=', $whereTwo->getOperator());
        $this->assertEquals('\74\65\73\74', $whereTwo->getValue());

    }

    public function test_where_contains()
    {
        $b = $this->newBuilder();

        $b->whereContains('cn', 'test');

        $where = $b->getWheres()[0];

        $this->assertEquals('cn', $where->getField());
        $this->assertEquals('contains', $where->getOperator());
        $this->assertEquals('\74\65\73\74', $where->getValue());
    }

    public function test_where_starts_with()
    {
        $b = $this->newBuilder();

        $b->whereStartsWith('cn', 'test');

        $where = $b->getWheres()[0];

        $this->assertEquals('cn', $where->getField());
        $this->assertEquals('starts_with', $where->getOperator());
        $this->assertEquals('\74\65\73\74', $where->getValue());
    }

    public function test_where_ends_with()
    {
        $b = $this->newBuilder();

        $b->whereEndsWith('cn', 'test');

        $where = $b->getWheres()[0];

        $this->assertEquals('cn', $where->getField());
        $this->assertEquals('ends_with', $where->getOperator());
        $this->assertEquals('\74\65\73\74', $where->getValue());
    }

    public function test_or_where()
    {
        $b = $this->newBuilder();

        $b->orWhere('cn', '=', 'test');

        $where = $b->getOrWheres()[0];

        $this->assertEquals('cn', $where->getField());
        $this->assertEquals('=', $where->getOperator());
        $this->assertEquals('\74\65\73\74', $where->getValue());
    }

    public function test_or_where_with_array()
    {
        $b = $this->newBuilder();

        $b->orWhere([
            'cn'    => 'test',
            'name'  => 'test',
        ]);

        $whereOne = $b->getOrWheres()[0];

        $this->assertEquals('cn', $whereOne->getField());
        $this->assertEquals('=', $whereOne->getOperator());
        $this->assertEquals('\74\65\73\74', $whereOne->getValue());

        $whereTwo = $b->getOrWheres()[1];

        $this->assertEquals('name', $whereTwo->getField());
        $this->assertEquals('=', $whereTwo->getOperator());
        $this->assertEquals('\74\65\73\74', $whereTwo->getValue());
    }

    public function test_or_where_contains()
    {
        $b = $this->newBuilder();

        $b->orWhereContains('cn', 'test');

        $where = $b->getOrWheres()[0];

        $this->assertEquals('cn', $where->getField());
        $this->assertEquals('contains', $where->getOperator());
        $this->assertEquals('\74\65\73\74', $where->getValue());
    }

    public function test_or_where_starts_with()
    {
        $b = $this->newBuilder();

        $b->orWhereStartsWith('cn', 'test');

        $where = $b->getOrWheres()[0];

        $this->assertEquals('cn', $where->getField());
        $this->assertEquals('starts_with', $where->getOperator());
        $this->assertEquals('\74\65\73\74', $where->getValue());
    }

    public function test_or_where_ends_with()
    {
        $b = $this->newBuilder();

        $b->orWhereEndsWith('cn', 'test');

        $where = $b->getOrWheres()[0];

        $this->assertEquals('cn', $where->getField());
        $this->assertEquals('ends_with', $where->getOperator());
        $this->assertEquals('\74\65\73\74', $where->getValue());
    }

    public function test_where_invalid_operator()
    {
        $b = $this->newBuilder();

        $this->setExpectedException('Adldap\Exceptions\InvalidQueryOperatorException');

        $b->where('field', 'invalid', 'value');
    }

    public function test_or_where_invalid_operator()
    {
        $b = $this->newBuilder();

        $this->setExpectedException('Adldap\Exceptions\InvalidQueryOperatorException');

        $b->orWhere('field', 'invalid', 'value');
    }

    public function test_built_where()
    {
        $b = $this->newBuilder();

        $b->where('field', '=' , 'value');

        $expected = '(field=\76\61\6c\75\65)';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function test_built_wheres()
    {
        $b = $this->newBuilder();

        $b->where('field', '=' , 'value');

        $b->where('other', '=', 'value');

        $expected = '(&(field=\76\61\6c\75\65)(other=\76\61\6c\75\65))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function test_built_where_starts_with()
    {
        $b = $this->newBuilder();

        $b->whereStartsWith('field', 'value');

        $expected = '(field=\76\61\6c\75\65*)';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function test_built_where_ends_with()
    {
        $b = $this->newBuilder();

        $b->whereEndsWith('field', 'value');

        $expected = '(field=*\76\61\6c\75\65)';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function test_built_where_contains()
    {
        $b = $this->newBuilder();

        $b->whereContains('field', 'value');

        $expected = '(field=*\76\61\6c\75\65*)';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function test_built_or_where()
    {
        $b = $this->newBuilder();

        $b->orWhere('field', '=' , 'value');

        $expected = '(field=\76\61\6c\75\65)';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function test_built_or_wheres()
    {
        $b = $this->newBuilder();

        $b->orWhere('field', '=' , 'value');

        $b->orWhere('other', '=', 'value');

        $expected = '(|(field=\76\61\6c\75\65)(other=\76\61\6c\75\65))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function test_built_or_where_starts_with()
    {
        $b = $this->newBuilder();

        $b->orWhereStartsWith('field', 'value');

        $expected = '(field=\76\61\6c\75\65*)';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function test_built_or_where_ends_with()
    {
        $b = $this->newBuilder();

        $b->orWhereEndsWith('field', 'value');

        $expected = '(field=*\76\61\6c\75\65)';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function test_built_or_where_contains()
    {
        $b = $this->newBuilder();

        $b->orWhereContains('field', 'value');

        $expected = '(field=*\76\61\6c\75\65*)';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function test_built_where_and_or_wheres()
    {
        $b = $this->newBuilder();

        $b->where('field', '=', 'value');

        $b->orWhere('or', '=', 'value');

        $expected = '(&(field=\76\61\6c\75\65)(|(or=\76\61\6c\75\65)))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function test_built_where_has()
    {
        $b = $this->newBuilder();

        $b->whereHas('field');

        $expected = '(field=*)';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function test_built_where_not_has()
    {
        $b = $this->newBuilder();

        $b->whereNotHas('field');

        $expected = '(!(field=*))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function test_built_where_not_contains()
    {
        $b = $this->newBuilder();

        $b->whereNotContains('field', 'value');

        $expected = '(!(field=*\76\61\6c\75\65*))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function test_built_where_approximately_equals()
    {
        $b = $this->newBuilder();

        $b->whereApproximatelyEquals('field', 'value');

        $expected = '(field~=\76\61\6c\75\65)';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function test_built_or_where_has()
    {
        $b = $this->newBuilder();

        $b->orWhereHas('field');

        $expected = '(field=*)';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function test_built_or_where_not_has()
    {
        $b = $this->newBuilder();

        $b->orWhereNotHas('field');

        $expected = '(!(field=*))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function test_built_or_where_approximately_equals()
    {
        $b = $this->newBuilder();

        $b->orWhereApproximatelyEquals('field', 'value');

        $expected = '(field~=\76\61\6c\75\65)';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function test_built_raw_filter()
    {
        $b = $this->newBuilder();

        $filter = '(field=value)';

        $b->rawFilter($filter);

        $this->assertEquals($filter, $b->getQuery());
    }

    public function test_built_raw_filter_with_wheres()
    {
        $b = $this->newBuilder();

        $b->rawFilter('(field=value)');

        $b->where('field', '=', 'value');

        $b->orWhere('field', '=', 'value');

        $expected = '(&(field=value)(field=\76\61\6c\75\65)(|(field=\76\61\6c\75\65)))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function test_built_raw_filter_multiple()
    {
        $b = $this->newBuilder();

        $b->rawFilter('(field=value)');

        $b->rawFilter('(|(field=value))');

        $b->rawFilter('(field=value)');

        $expected = '(&(field=value)(|(field=value))(field=value))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function test_built_where_enabled()
    {
        $b = $this->newBuilder();

        $b->whereEnabled();

        $this->assertEquals('(!(UserAccountControl:1.2.840.113556.1.4.803:=2))', $b->getQuery());
    }

    public function test_built_where_disabled()
    {
        $b = $this->newBuilder();

        $b->whereDisabled();

        $this->assertEquals('(UserAccountControl:1.2.840.113556.1.4.803:=2)', $b->getQuery());
    }

    public function test_paginate_with_no_results()
    {
        $connection = $this->newConnectionMock();

        $connection->shouldReceive('controlPagedResult')->once()->withArgs([50, true, '']);
        $connection->shouldReceive('search')->once()->withArgs(['', '(field=\76\61\6c\75\65)', []])->andReturn(null);

        $b = $this->newBuilder($connection);

        $this->assertFalse($b->where('field', '=', 'value')->paginate(50));
    }

    public function test_paginate_with_results()
    {
        $connection = $this->newConnectionMock();

        $rawEntries = [
            'count' => 1,
            [
                'cn'                => ['Test'],
                'distinguishedname' => ['cn=Test,dc=corp,dc=acme,dc=org'],
            ],
        ];

        $connection->shouldReceive('controlPagedResult')->once()->withArgs([50, true, '']);
        $connection->shouldReceive('search')->once()->withArgs(['', '(field=\76\61\6c\75\65)', []])->andReturn('resource');
        $connection->shouldReceive('controlPagedResultResponse')->withArgs(['resource', '']);
        $connection->shouldReceive('getEntries')->andReturn($rawEntries);

        $b = $this->newBuilder($connection);

        $paginator = $b->where('field', '=', 'value')->paginate(50);

        $this->assertInstanceOf('Adldap\Objects\Paginator', $paginator);
        $this->assertEquals(1, $paginator->getPages());
        $this->assertEquals(1, $paginator->count());

        foreach($paginator as $model) {
            $this->assertInstanceOf('Adldap\Models\AbstractModel', $model);
            $this->assertEquals($rawEntries[0]['cn'][0], $model->getCommonName());
            $this->assertEquals($rawEntries[0]['distinguishedname'][0], $model->getDn());
        }
    }

    public function test_field_is_escaped()
    {
        $b = $this->newBuilder();

        $field = '*^&.:foo()-=';

        $value = 'testing';

        $b->where($field, '=', $value);

        $utils = $this->mock('Adldap\Utilities')->makePartial()->shouldAllowMockingProtectedMethods();

        $escapedField = $utils->escape($field, null, 3);

        $escapedValue = $utils->escape($value);

        $this->assertEquals("($escapedField=$escapedValue)", $b->getQuery());
    }

    public function test_builder_dn_is_applied_to_new_instance()
    {
        $b = $this->newBuilder();

        $b->setDn('New DN');

        $newB = $b->newInstance();

        $this->assertEquals('New DN', $newB->getDn());
    }

    public function test_add_binding()
    {
        $b = $this->newBuilder();

        $where = new Where('cn', '=', 'test');
        $orWhere = new OrWhere('cn', '=', 'test');
        $filter = new Filter('(cn=test)');
        $select = new Select('cn');

        $b->addBinding($where);
        $b->addBinding($orWhere, 'orWhere');
        $b->addBinding($filter, 'filter');
        $b->addBinding($select, 'select');

        $this->assertEquals(1, count($b->getWheres()));
        $this->assertInstanceOf('Adldap\Query\Bindings\Where', $b->getWheres()[0]);

        $this->assertEquals(1, count($b->getOrWheres()));
        $this->assertInstanceOf('Adldap\Query\Bindings\OrWhere', $b->getOrWheres()[0]);

        $this->assertEquals(1, count($b->getFilters()));
        $this->assertInstanceOf('Adldap\Query\Bindings\Filter', $b->getFilters()[0]);

        $this->assertEquals(4, count($b->getSelects()));
        $this->assertInstanceOf('Adldap\Query\Bindings\Select', $b->getSelects()[0]);
    }

    public function test_add_invalid_binding()
    {
        $b = $this->newBuilder();

        $this->setExpectedException('InvalidArgumentException');

        $b->addBinding(new Filter('filter'), 'invalid');
    }

    public function test_select_args()
    {
        $b = $this->newBuilder();

        $selects = $b->select('attr1', 'attr2', 'attr3')->getSelects();

        $this->assertCount(6, $selects);
        $this->assertEquals('attr1', $selects[0]);
        $this->assertEquals('attr2', $selects[1]);
        $this->assertEquals('attr3', $selects[2]);
    }

    public function test_dynamic_where()
    {
        $b = $this->newBuilder();

        $b->whereCn('test');

        $wheres = $b->getWheres();

        $where = end($wheres);

        $this->assertCount(1, $wheres);
        $this->assertEquals('cn', $where->getField());
        $this->assertEquals('=', $where->getOperator());
        $this->assertEquals('\74\65\73\74', $where->getValue());
    }

    public function test_dynamic_and_where()
    {
        $b = $this->newBuilder();

        $b->whereCnAndSn('cn', 'sn');

        $wheres = $b->getWheres();

        $whereCn = $wheres[0];
        $whereSn = $wheres[1];

        $this->assertCount(2, $wheres);

        $this->assertEquals('cn', $whereCn->getField());
        $this->assertEquals('=', $whereCn->getOperator());
        $this->assertEquals('\63\6e', $whereCn->getValue());

        $this->assertEquals('sn', $whereSn->getField());
        $this->assertEquals('=', $whereSn->getOperator());
        $this->assertEquals('\73\6e', $whereSn->getValue());
    }

    public function test_dynamic_or_where()
    {
        $b = $this->newBuilder();

        $b->whereCnOrSn('cn', 'sn');

        $wheres = $b->getWheres();
        $orWheres = $b->getOrWheres();

        $whereCn = end($wheres);
        $orWhereSn = end($orWheres);

        $this->assertCount(1, $wheres);
        $this->assertCount(1, $orWheres);

        $this->assertEquals('cn', $whereCn->getField());
        $this->assertEquals('=', $whereCn->getOperator());
        $this->assertEquals('\63\6e', $whereCn->getValue());

        $this->assertEquals('sn', $orWhereSn->getField());
        $this->assertEquals('=', $orWhereSn->getOperator());
        $this->assertEquals('\73\6e', $orWhereSn->getValue());
    }
}
