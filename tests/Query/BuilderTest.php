<?php

namespace Adldap\Tests\Query;

use DateTime;
use Adldap\Models\Model;
use Adldap\Tests\TestCase;
use Adldap\Query\Paginator;
use Adldap\Schemas\SchemaInterface;
use Adldap\Connections\ConnectionInterface;
use Illuminate\Support\Collection;

class BuilderTest extends TestCase
{
    public function test_construct()
    {
        $b = $this->newBuilder();

        $this->assertEmpty($b->getQuery());
    }

    public function test_select_array()
    {
        $b = $this->newBuilder();

        $b->select(['testing']);

        $this->assertEquals([
            'testing',
            'objectcategory',
            'objectclass',
        ], $b->getSelects());
    }

    public function test_select_string()
    {
        $b = $this->newBuilder();

        $b->select('testing');

        $this->assertEquals([
            'testing',
            'objectcategory',
            'objectclass',
        ], $b->getSelects());
    }

    public function test_select_empty_string()
    {
        $b = $this->newBuilder();

        $b->select('');

        $this->assertEquals([
            '',
            'objectcategory',
            'objectclass',
        ], $b->getSelects());
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

        $where = $b->filters['and'][0];

        $this->assertEquals('cn', $where['field']);
        $this->assertEquals('=', $where['operator']);
        $this->assertEquals('\74\65\73\74', $where['value']);
    }

    public function test_where_with_array()
    {
        $b = $this->newBuilder();

        $b->where([
            'cn'    => 'test',
            'name'  => 'test',
        ]);

        $whereOne = $b->filters['and'][0];

        $this->assertEquals('cn', $whereOne['field']);
        $this->assertEquals('=', $whereOne['operator']);
        $this->assertEquals('\74\65\73\74', $whereOne['value']);

        $whereTwo = $b->filters['and'][1];

        $this->assertEquals('name', $whereTwo['field']);
        $this->assertEquals('=', $whereTwo['operator']);
        $this->assertEquals('\74\65\73\74', $whereTwo['value']);
    }

    public function test_where_with_nested_arrays()
    {
        $b = $this->newBuilder();

        $b->where([
            ['cn', '=', 'test'],
            ['whencreated', '>=', 'test']
        ]);

        $whereOne = $b->filters['and'][0];

        $this->assertEquals('cn', $whereOne['field']);
        $this->assertEquals('=', $whereOne['operator']);
        $this->assertEquals('\74\65\73\74', $whereOne['value']);

        $whereTwo = $b->filters['and'][1];

        $this->assertEquals('whencreated', $whereTwo['field']);
        $this->assertEquals('>=', $whereTwo['operator']);
        $this->assertEquals('\74\65\73\74', $whereTwo['value']);

        $this->assertEquals('(&(cn=test)(whencreated>=test))', $b->getUnescapedQuery());
    }

    public function test_where_contains()
    {
        $b = $this->newBuilder();

        $b->whereContains('cn', 'test');

        $where = $b->filters['and'][0];

        $this->assertEquals('cn', $where['field']);
        $this->assertEquals('contains', $where['operator']);
        $this->assertEquals('\74\65\73\74', $where['value']);
        $this->assertEquals('(cn=*test*)', $b->getUnescapedQuery());
    }

    public function test_where_starts_with()
    {
        $b = $this->newBuilder();

        $b->whereStartsWith('cn', 'test');

        $where = $b->filters['and'][0];

        $this->assertEquals('cn', $where['field']);
        $this->assertEquals('starts_with', $where['operator']);
        $this->assertEquals('\74\65\73\74', $where['value']);
        $this->assertEquals('(cn=test*)', $b->getUnescapedQuery());
    }

    public function test_where_not_starts_with()
    {
        $b = $this->newBuilder();

        $b->whereNotStartsWith('cn', 'test');

        $where = $b->filters['and'][0];

        $this->assertEquals('cn', $where['field']);
        $this->assertEquals('not_starts_with', $where['operator']);
        $this->assertEquals('\74\65\73\74', $where['value']);
        $this->assertEquals('(!(cn=test*))', $b->getUnescapedQuery());
    }

    public function test_where_ends_with()
    {
        $b = $this->newBuilder();

        $b->whereEndsWith('cn', 'test');

        $where = $b->filters['and'][0];

        $this->assertEquals('cn', $where['field']);
        $this->assertEquals('ends_with', $where['operator']);
        $this->assertEquals('\74\65\73\74', $where['value']);
        $this->assertEquals('(cn=*test)', $b->getUnescapedQuery());
    }

    public function test_where_not_ends_with()
    {
        $b = $this->newBuilder();

        $b->whereNotEndsWith('cn', 'test');

        $where = $b->filters['and'][0];

        $this->assertEquals('cn', $where['field']);
        $this->assertEquals('not_ends_with', $where['operator']);
        $this->assertEquals('\74\65\73\74', $where['value']);
        $this->assertEquals('(!(cn=*test))', $b->getUnescapedQuery());
    }

    public function test_where_between()
    {
        $from = (new DateTime('October 1st 2016'))->format('YmdHis.0\Z');
        $to = (new DateTime('January 1st 2017'))->format('YmdHis.0\Z');

        $b = $this->newBuilder();

        $b->whereBetween('whencreated', [$from, $to]);

        $this->assertEquals('(&(whencreated>=20161001000000.0Z)(whencreated<=20170101000000.0Z))', $b->getUnescapedQuery());
    }

    public function test_where_member_of()
    {
        $b = $this->newBuilder();

        $b->whereMemberOf('cn=Accounting,dc=org,dc=acme');

        $where = $b->filters['and'][0];

        $this->assertEquals('memberof:1.2.840.113556.1.4.1941:', $where['field']);
        $this->assertEquals('=', $where['operator']);
        $this->assertEquals('\63\6e\3d\41\63\63\6f\75\6e\74\69\6e\67\2c\64\63\3d\6f\72\67\2c\64\63\3d\61\63\6d\65', $where['value']);
        $this->assertEquals('(memberof:1.2.840.113556.1.4.1941:=cn=Accounting,dc=org,dc=acme)', $b->getUnescapedQuery());
    }

    public function test_or_where()
    {
        $b = $this->newBuilder();

        $b->orWhere('cn', '=', 'test');

        $where = $b->filters['or'][0];

        $this->assertEquals('cn', $where['field']);
        $this->assertEquals('=', $where['operator']);
        $this->assertEquals('\74\65\73\74', $where['value']);
    }

    public function test_or_where_with_array()
    {
        $b = $this->newBuilder();

        $b->orWhere([
            'cn'    => 'test',
            'name'  => 'test',
        ]);

        $whereOne = $b->filters['or'][0];

        $this->assertEquals('cn', $whereOne['field']);
        $this->assertEquals('=', $whereOne['operator']);
        $this->assertEquals('\74\65\73\74', $whereOne['value']);

        $whereTwo = $b->filters['or'][1];

        $this->assertEquals('name', $whereTwo['field']);
        $this->assertEquals('=', $whereTwo['operator']);
        $this->assertEquals('\74\65\73\74', $whereTwo['value']);

        $this->assertEquals('(|(cn=test)(name=test))', $b->getUnescapedQuery());
    }

    public function test_or_where_with_nested_arrays()
    {
        $b = $this->newBuilder();

        $b->orWhere([
            ['one', '=', 'one'],
            ['two', 'contains', 'two'],
            ['three', '*'],
        ]);

        $this->assertEquals('(|(one=one)(two=*two*)(three=*))', $b->getUnescapedQuery());
    }

    public function test_or_where_contains()
    {
        $b = $this->newBuilder();

        $b
            ->whereContains('name', 'test')
            ->orWhereContains('cn', 'test');

        $where = $b->filters['or'][0];

        $this->assertEquals('cn', $where['field']);
        $this->assertEquals('contains', $where['operator']);
        $this->assertEquals('\74\65\73\74', $where['value']);


        $this->assertEquals('(&(name=*test*)(|(cn=*test*)))', $b->getUnescapedQuery());
    }

    public function test_or_where_starts_with()
    {
        $b = $this->newBuilder();

        $b
            ->whereStartsWith('name', 'test')
            ->orWhereStartsWith('cn', 'test');

        $where = $b->filters['or'][0];

        $this->assertEquals('cn', $where['field']);
        $this->assertEquals('starts_with', $where['operator']);
        $this->assertEquals('\74\65\73\74', $where['value']);
        $this->assertEquals('(&(name=test*)(|(cn=test*)))', $b->getUnescapedQuery());
    }

    public function test_or_where_ends_with()
    {
        $b = $this->newBuilder();

        $b
            ->whereEndsWith('name', 'test')
            ->orWhereEndsWith('cn', 'test');

        $where = $b->filters['or'][0];

        $this->assertEquals('cn', $where['field']);
        $this->assertEquals('ends_with', $where['operator']);
        $this->assertEquals('\74\65\73\74', $where['value']);
        $this->assertEquals('(&(name=*test)(|(cn=*test)))', $b->getUnescapedQuery());
    }

    public function test_or_where_member_of()
    {
        $b = $this->newBuilder();

        $b->orWhereEquals('cn', 'John Doe');
        $b->orWhereMemberOf('cn=Accounting,dc=org,dc=acme');

        $where = $b->filters['or'][1];

        $this->assertEquals('memberof:1.2.840.113556.1.4.1941:', $where['field']);
        $this->assertEquals('=', $where['operator']);
        $this->assertEquals('\63\6e\3d\41\63\63\6f\75\6e\74\69\6e\67\2c\64\63\3d\6f\72\67\2c\64\63\3d\61\63\6d\65', $where['value']);
        $this->assertEquals(
            '(|(cn=John Doe)(memberof:1.2.840.113556.1.4.1941:=cn=Accounting,dc=org,dc=acme))',
            $b->getUnescapedQuery()
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_where_invalid_operator()
    {
        $b = $this->newBuilder();

        $b->where('field', 'invalid', 'value');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_or_where_invalid_operator()
    {
        $b = $this->newBuilder();

        $b->orWhere('field', 'invalid', 'value');
    }

    public function test_built_where()
    {
        $b = $this->newBuilder();

        $b->where('field', '=', 'value');

        $this->assertEquals('(field=value)', $b->getUnescapedQuery());
    }

    public function test_built_wheres()
    {
        $b = $this->newBuilder();

        $b->where('field', '=', 'value');

        $b->where('other', '=', 'value');

        $this->assertEquals('(&(field=value)(other=value))', $b->getUnescapedQuery());
    }

    public function test_built_where_starts_with()
    {
        $b = $this->newBuilder();

        $b->whereStartsWith('field', 'value');

        $this->assertEquals('(field=value*)', $b->getUnescapedQuery());
    }

    public function test_built_where_ends_with()
    {
        $b = $this->newBuilder();

        $b->whereEndsWith('field', 'value');

        $this->assertEquals('(field=*value)', $b->getUnescapedQuery());
    }

    public function test_built_where_contains()
    {
        $b = $this->newBuilder();

        $b->whereContains('field', 'value');

        $this->assertEquals('(field=*value*)', $b->getUnescapedQuery());
    }

    public function test_built_or_where()
    {
        $b = $this->newBuilder();

        $b->orWhere('field', '=', 'value');

        $this->assertEquals('(field=value)', $b->getUnescapedQuery());
    }

    public function test_built_or_wheres()
    {
        $b = $this->newBuilder();

        $b->orWhere('field', '=', 'value');

        $b->orWhere('other', '=', 'value');

        $this->assertEquals('(|(field=value)(other=value))', $b->getUnescapedQuery());
    }

    public function test_built_or_where_starts_with()
    {
        $b = $this->newBuilder();

        $b->orWhereStartsWith('field', 'value');

        $this->assertEquals('(field=value*)', $b->getUnescapedQuery());
    }

    public function test_built_or_where_ends_with()
    {
        $b = $this->newBuilder();

        $b->orWhereEndsWith('field', 'value');

        $this->assertEquals('(field=*value)', $b->getUnescapedQuery());
    }

    public function test_built_or_where_contains()
    {
        $b = $this->newBuilder();

        $b->orWhereContains('field', 'value');

        $this->assertEquals('(field=*value*)', $b->getUnescapedQuery());
    }

    public function test_built_where_and_or_wheres()
    {
        $b = $this->newBuilder();

        $b->where('field', '=', 'value');

        $b->orWhere('or', '=', 'value');

        $this->assertEquals('(&(field=value)(|(or=value)))', $b->getUnescapedQuery());
    }

    public function test_built_where_has()
    {
        $b = $this->newBuilder();

        $b->whereHas('field');

        $this->assertEquals('(field=*)', $b->getQuery());
    }

    public function test_built_where_not_has()
    {
        $b = $this->newBuilder();

        $b->whereNotHas('field');

        $this->assertEquals('(!(field=*))', $b->getQuery());
    }

    public function test_built_where_not_contains()
    {
        $b = $this->newBuilder();

        $b->whereNotContains('field', 'value');

        $this->assertEquals('(!(field=*value*))', $b->getUnescapedQuery());
    }

    public function test_built_where_approximately_equals()
    {
        $b = $this->newBuilder();

        $b->whereApproximatelyEquals('field', 'value');

        $this->assertEquals('(field~=value)', $b->getUnescapedQuery());
    }

    public function test_built_or_where_has()
    {
        $b = $this->newBuilder();

        $b->orWhereHas('field');

        $this->assertEquals('(field=*)', $b->getUnescapedQuery());
    }

    public function test_built_or_where_has_multiple()
    {
        $b = $this->newBuilder();

        $b->orWhereHas('one')
            ->orWhereHas('two');

        $this->assertEquals('(|(one=*)(two=*))', $b->getQuery());
    }

    public function test_built_or_where_not_has()
    {
        $b = $this->newBuilder();

        $b->orWhereNotHas('field');

        $this->assertEquals('(!(field=*))', $b->getQuery());
    }

    public function test_built_where_equals()
    {
        $b = $this->newBuilder();

        $b->whereEquals('field', 'value');

        $this->assertEquals('(field=value)', $b->getUnescapedQuery());
    }

    public function test_built_where_not_equals()
    {
        $b = $this->newBuilder();

        $b->whereNotEquals('field', 'value');

        $this->assertEquals('(!(field=value))', $b->getUnescapedQuery());
    }

    public function test_built_or_where_equals()
    {
        $b = $this->newBuilder();

        $b->orWhereEquals('field', 'value');

        // Due to only one 'orWhere' in the current query,
        // a standard filter should be constructed.
        $this->assertEquals('(field=value)', $b->getUnescapedQuery());
    }

    public function test_built_or_where_not_equals()
    {
        $b = $this->newBuilder();

        $b->orWhereNotEquals('field', 'value');

        $this->assertEquals('(!(field=value))', $b->getUnescapedQuery());
    }

    public function test_built_or_where_approximately_equals()
    {
        $b = $this->newBuilder();

        $b->orWhereApproximatelyEquals('field', 'value');

        $this->assertEquals('(field~=value)', $b->getUnescapedQuery());
    }

    public function test_built_raw_filter()
    {
        $b = $this->newBuilder();

        $b->rawFilter('(field=value)');

        $this->assertEquals('(field=value)', $b->getQuery());
    }

    public function test_built_raw_filter_with_wheres()
    {
        $b = $this->newBuilder();

        $b->rawFilter('(field=value)');

        $b->where('field', '=', 'value');

        $b->orWhere('field', '=', 'value');

        $this->assertEquals('(&(field=value)(field=value)(|(field=value)))', $b->getUnescapedQuery());
    }

    public function test_built_raw_filter_multiple()
    {
        $b = $this->newBuilder();

        $b->rawFilter('(field=value)');

        $b->rawFilter('(|(field=value))');

        $b->rawFilter('(field=value)');

        $this->assertEquals('(&(field=value)(|(field=value))(field=value))', $b->getQuery());
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

        $connection->shouldReceive('controlPagedResult')->once()->withArgs([50, true, ''])
            ->shouldReceive('search')->once()->withArgs(['', '(field=\76\61\6c\75\65)', ['*']])->andReturn(null)
            ->shouldReceive('controlPagedResult')->once();

        $b = $this->newBuilder($connection);

        $this->assertInstanceOf(Paginator::class, $b->where('field', '=', 'value')->paginate(50));
    }

    public function test_paginate_with_results()
    {
        $connection = $this->newConnectionMock();

        $rawEntries = [
            'count' => 1,
            [
                'dn' => 'cn=Test,dc=corp,dc=acme,dc=org',
                'cn' => ['Test'],
            ],
        ];

        $connection->shouldReceive('controlPagedResult')->twice()
            ->shouldReceive('search')->once()->withArgs(['', '(field=\76\61\6c\75\65)', ['*']])->andReturn('resource')
            ->shouldReceive('controlPagedResultResponse')->withArgs(['resource', ''])
            ->shouldReceive('getEntries')->andReturn($rawEntries);

        $b = $this->newBuilder($connection);

        $paginator = $b->where('field', '=', 'value')->paginate(50);

        $this->assertInstanceOf(Paginator::class, $paginator);
        $this->assertEquals(1, $paginator->getPages());
        $this->assertEquals(1, $paginator->count());

        foreach ($paginator as $model) {
            $this->assertInstanceOf(Model::class, $model);
            $this->assertEquals($rawEntries[0]['dn'], $model->getDn());
            $this->assertEquals($rawEntries[0]['cn'][0], $model->getCommonName());
        }
    }

    public function test_field_is_escaped()
    {
        $b = $this->newBuilder();

        $field = '*^&.:foo()-=';

        $value = 'testing';

        $b->where($field, '=', $value);

        $escapedField = ldap_escape($field, null, 3);

        $escapedValue = ldap_escape($value);

        $this->assertEquals("($escapedField=$escapedValue)", $b->getQuery());
    }

    public function test_builder_dn_is_applied_to_new_instance()
    {
        $b = $this->newBuilder();

        $b->setDn('New DN');

        $newB = $b->newInstance();

        $this->assertEquals('New DN', $newB->getDn());
    }

    public function test_select_args()
    {
        $b = $this->newBuilder();

        $selects = $b->select('attr1', 'attr2', 'attr3')->getSelects();

        $this->assertCount(5, $selects);
        $this->assertEquals('attr1', $selects[0]);
        $this->assertEquals('attr2', $selects[1]);
        $this->assertEquals('attr3', $selects[2]);
    }

    public function test_dynamic_where()
    {
        $b = $this->newBuilder();

        $b->whereCn('test');

        $where = $b->filters['and'][0];

        $this->assertEquals('cn', $where['field']);
        $this->assertEquals('=', $where['operator']);
        $this->assertEquals('\74\65\73\74', $where['value']);
    }

    public function test_dynamic_and_where()
    {
        $b = $this->newBuilder();

        $b->whereCnAndSn('cn', 'sn');

        $wheres = $b->filters['and'];

        $whereCn = $wheres[0];
        $whereSn = $wheres[1];

        $this->assertCount(2, $wheres);

        $this->assertEquals('cn', $whereCn['field']);
        $this->assertEquals('=', $whereCn['operator']);
        $this->assertEquals('\63\6e', $whereCn['value']);

        $this->assertEquals('sn', $whereSn['field']);
        $this->assertEquals('=', $whereSn['operator']);
        $this->assertEquals('\73\6e', $whereSn['value']);
    }

    public function test_dynamic_or_where()
    {
        $b = $this->newBuilder();

        $b->whereCnOrSn('cn', 'sn');

        $wheres = $b->filters['and'];
        $orWheres = $b->filters['or'];

        $whereCn = end($wheres);
        $orWhereSn = end($orWheres);

        $this->assertCount(1, $wheres);
        $this->assertCount(1, $orWheres);

        $this->assertEquals('cn', $whereCn['field']);
        $this->assertEquals('=', $whereCn['operator']);
        $this->assertEquals('\63\6e', $whereCn['value']);

        $this->assertEquals('sn', $orWhereSn['field']);
        $this->assertEquals('=', $orWhereSn['operator']);
        $this->assertEquals('\73\6e', $orWhereSn['value']);
    }

    public function test_selects_are_not_overwritten_with_empty_array()
    {
        $b = $this->newBuilder();

        $b->select(['one', 'two']);

        $b->select([]);

        $this->assertEquals(['one', 'two', 'objectcategory', 'objectclass'], $b->getSelects());
    }

    public function test_nested_or_filter()
    {
        $b = $this->newBuilder();

        $query = $b->orFilter(function ($query) {
            $query->where([
                'one' => 'one',
                'two' => 'two',
            ]);
        })->getUnescapedQuery();

        $this->assertEquals('(|(one=one)(two=two))', $query);
    }

    public function test_nested_and_filter()
    {
        $b = $this->newBuilder();

        $query = $b->andFilter(function ($query) {
            $query->where([
                'one' => 'one',
                'two' => 'two',
            ]);
        })->getUnescapedQuery();

        $this->assertEquals('(&(one=one)(two=two))', $query);
    }

    public function test_nested_not_filter()
    {
        $b = $this->newBuilder();

        $query = $b->notFilter(function ($query) {
             $query->where([
                 'one' => 'one',
                 'two' => 'two',
             ]);
        })->getUnescapedQuery();

        $this->assertEquals('(!(one=one)(two=two))', $query);
    }

    public function test_nested_filters()
    {
        $b = $this->newBuilder();

        $query = $b->orFilter(function ($query) {
            $query->where([
                'one' => 'one',
                'two' => 'two',
            ]);
        })->andFilter(function ($query) {
            $query->where([
                'one' => 'one',
                'two' => 'two',
            ]);
        })->getUnescapedQuery();

        $this->assertEquals('(&(|(one=one)(two=two))(&(one=one)(two=two)))', $query);
    }

    public function test_nested_filters_with_non_nested()
    {
        $b = $this->newBuilder();

        $query = $b->orFilter(function ($query) {
            $query->where([
                'one' => 'one',
                'two' => 'two',
            ]);
        })->andFilter(function ($query) {
            $query->where([
                'three' => 'three',
                'four' => 'four',
            ]);
        })->where([
            'five' => 'five',
            'six' => 'six',
        ])->getUnescapedQuery();

        $this->assertEquals('(&(|(one=one)(two=two))(&(three=three)(four=four))(five=five)(six=six))', $query);
    }

    public function test_nested_builder_is_nested()
    {
        $b = $this->newBuilder();

        $b->andFilter(function ($q) use (&$query) {
            $query = $q;
        });

        $this->assertTrue($query->isNested());
        $this->assertFalse($b->isNested());
    }

    public function test_new_nested_instance_is_nested()
    {
        $b = $this->newBuilder();

        $this->assertTrue($b->newNestedInstance()->isNested());
    }

    public function test_does_not_equal()
    {
        $b = $this->newBuilder();

        $b->where('field', '!', 'value');

        $this->assertEquals('(!(field=value))', $b->getUnescapedQuery());
    }

    public function test_does_not_equal_alias()
    {
        $b = $this->newBuilder();

        $b->where('field', '!=', 'value');

        $this->assertEquals('(!(field=value))', $b->getUnescapedQuery());
    }

    public function test_using_both_equals_and_equals_alias_outputs_same_result()
    {
        $b = $this->newBuilder();

        $b
            ->where('field', '!=', 'value')
            ->where('other', '!', 'value');

        $this->assertEquals('(&(!(field=value))(!(other=value)))', $b->getUnescapedQuery());
    }

    public function test_find_by_dn_returns_array_when_raw_result_is_requested()
    {
        $c = $this->mock(ConnectionInterface::class);
        $s = $this->mock(SchemaInterface::class);

        $b = $this->newBuilder($c);

        $b->setSchema($s);

        $dn = 'cn=John Doe,dc=acme,dc=org';

        $rawEntries = [
            'count' => 1,
            [
                'dn' => $dn,
                'cn' => ['John Doe'],
            ],
        ];

        $s->shouldReceive('objectClass')->andReturn('objectclass');

        $c
            ->shouldReceive('read')->once()->with('cn=John Doe,dc=acme,dc=org', '(objectclass=*)',  [0 => '*'], false, 1)
            ->shouldReceive('getEntries')->once()->andReturn($rawEntries);

        $this->assertEquals($rawEntries[0], $b->raw()->findByDn($dn));
    }

    public function test_find_does_not_use_anr_when_using_other_ldap_distro()
    {
        $c = $this->mock(ConnectionInterface::class);
        $s = $this->mock(SchemaInterface::class);

        $b = $this->newBuilder($c);

        $b->setSchema($s);

        $s
            ->shouldReceive('name')->once()->andReturn('name')
            ->shouldReceive('email')->once()->andReturn('mail')
            ->shouldReceive('userId')->times(3)->andReturn('uid')
            ->shouldReceive('lastName')->once()->andReturn('sn')
            ->shouldReceive('firstName')->once()->andReturn('givenname')
            ->shouldReceive('commonName')->once()->andReturn('cn')
            ->shouldReceive('displayName')->once()->andReturn('displayname')
            ->shouldReceive('objectCategory')->once()->andReturn('objectcategory')
            ->shouldReceive('objectClass')->once()->andReturn('objectclass');

        $expectedFilter = '(|(name=\6a\64\6f\65)(mail=\6a\64\6f\65)(uid=\6a\64\6f\65)(sn=\6a\64\6f\65)(givenname=\6a\64\6f\65)(cn=\6a\64\6f\65)(displayname=\6a\64\6f\65))';

        $select = ['cn', 'sn'];

        $expectedSelect = array_merge($select, [
            'objectcategory',
            'objectclass',
        ]);

        $c
            ->shouldReceive('search')->once()->with(null, $expectedFilter, $expectedSelect, $attrsOnly = false, $total = 1)->andReturnSelf()
            ->shouldReceive('getEntries')->once()->andReturn(null);

        $this->assertNull($b->find('jdoe', $select));
    }


    public function test_find_many_does_not_use_anr_when_using_other_ldap_distro()
    {
        $c = $this->mock(ConnectionInterface::class);
        $s = $this->mock(SchemaInterface::class);

        $b = $this->newBuilder($c);

        $b->setSchema($s);

        $s
            ->shouldReceive('name')->times(3)->andReturn('name')
            ->shouldReceive('email')->times(3)->andReturn('mail')
            ->shouldReceive('userId')->times(3)->andReturn('uid')
            ->shouldReceive('lastName')->times(3)->andReturn('sn')
            ->shouldReceive('firstName')->times(3)->andReturn('givenname')
            ->shouldReceive('commonName')->times(3)->andReturn('cn')
            ->shouldReceive('displayName')->times(3)->andReturn('displayname')
            ->shouldReceive('objectCategory')->once()->andReturn('objectcategory')
            ->shouldReceive('objectClass')->once()->andReturn('objectclass');

        $expectedOrFilters = [
            '(|(name=\6a\6f\68\6e)(mail=\6a\6f\68\6e)(uid=\6a\6f\68\6e)(sn=\6a\6f\68\6e)(givenname=\6a\6f\68\6e)(cn=\6a\6f\68\6e)(displayname=\6a\6f\68\6e))',
            '(|(name=\6a\61\6e\65)(mail=\6a\61\6e\65)(uid=\6a\61\6e\65)(sn=\6a\61\6e\65)(givenname=\6a\61\6e\65)(cn=\6a\61\6e\65)(displayname=\6a\61\6e\65))',
            '(|(name=\73\75\65)(mail=\73\75\65)(uid=\73\75\65)(sn=\73\75\65)(givenname=\73\75\65)(cn=\73\75\65)(displayname=\73\75\65))'
        ];

        $expectedFilter = sprintf('(&%s)', implode($expectedOrFilters));

        $select = ['cn', 'sn'];

        $expectedSelect = array_merge($select, [
            'objectcategory',
            'objectclass',
        ]);

        $c
            ->shouldReceive('search')->once()->with(null, $expectedFilter, $expectedSelect, $attrsOnly = false, $total = 0)->andReturnSelf()
            ->shouldReceive('getEntries')->once()->andReturn(null);

        $this->assertInstanceOf(Collection::class, $b->findMany([
            'john',
            'jane',
            'sue',
        ], $select));
    }
}
