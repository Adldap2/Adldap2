<?php

namespace Adldap\Tests\Query;

use Adldap\Query\Builder;
use Adldap\Tests\UnitTestCase;

class BuilderTest extends UnitTestCase
{
    public function testConstruct()
    {
        $b = new Builder();

        $this->assertEmpty($b->getQuery());
    }

    public function testGetSelectsAreEmpty()
    {
        $b = new Builder();

        $this->assertEquals([], $b->getSelects());
    }

    public function testSelectArray()
    {
        $b = new Builder();

        $b->select(['testing']);

        $expected = [
            'testing',
            'objectcategory',
            'dn',
        ];

        $this->assertEquals($expected,  $b->getSelects());
    }

    public function testSelectString()
    {
        $b = new Builder();

        $b->select('testing');

        $expected = [
            'testing',
            'objectcategory',
            'dn',
        ];

        $this->assertEquals($expected,  $b->getSelects());
    }

    public function testSelectEmptyString()
    {
        $b = new Builder();

        $b->select('');

        $this->assertEquals([],  $b->getSelects());
    }

    public function testHasSelects()
    {
        $b = new Builder();

        $b->select('test');

        $this->assertTrue($b->hasSelects());
    }

    public function testWhere()
    {
        $b = new Builder();

        $b->where('cn', '=', 'test');

        $wheres = [
            [
                'field' => 'cn',
                'operator' => '=',
                'value' => '\74\65\73\74',
            ]
        ];

        $this->assertEquals($wheres, $b->getWheres());
    }

    public function testWhereContains()
    {
        $b = new Builder();

        $b->whereContains('cn', 'test');

        $wheres = [
            [
                'field' => 'cn',
                'operator' => 'contains',
                'value' => '\74\65\73\74',
            ]
        ];

        $this->assertEquals($wheres, $b->getWheres());
    }

    public function testWhereStartsWith()
    {
        $b = new Builder();

        $b->whereStartsWith('cn', 'test');

        $wheres = [
            [
                'field' => 'cn',
                'operator' => 'starts_with',
                'value' => '\74\65\73\74',
            ]
        ];

        $this->assertEquals($wheres, $b->getWheres());
    }

    public function testWhereEndsWith()
    {
        $b = new Builder();

        $b->whereEndsWith('cn', 'test');

        $wheres = [
            [
                'field' => 'cn',
                'operator' => 'ends_with',
                'value' => '\74\65\73\74',
            ]
        ];

        $this->assertEquals($wheres, $b->getWheres());
    }

    public function testOrWhere()
    {
        $b = new Builder();

        $b->orWhere('cn', '=', 'test');

        $wheres = [
            [
                'field' => 'cn',
                'operator' => '=',
                'value' => '\74\65\73\74',
            ]
        ];

        $this->assertEquals($wheres, $b->getOrWheres());
    }

    public function testOrWhereContains()
    {
        $b = new Builder();

        $b->orWhereContains('cn', 'test');

        $wheres = [
            [
                'field' => 'cn',
                'operator' => 'contains',
                'value' => '\74\65\73\74',
            ]
        ];

        $this->assertEquals($wheres, $b->getOrWheres());
    }

    public function testOrWhereStartsWith()
    {
        $b = new Builder();

        $b->orWhereStartsWith('cn', 'test');

        $wheres = [
            [
                'field' => 'cn',
                'operator' => 'starts_with',
                'value' => '\74\65\73\74',
            ]
        ];

        $this->assertEquals($wheres, $b->getOrWheres());
    }

    public function testOrWhereEndsWith()
    {
        $b = new Builder();

        $b->orWhereEndsWith('cn', 'test');

        $wheres = [
            [
                'field' => 'cn',
                'operator' => 'ends_with',
                'value' => '\74\65\73\74',
            ]
        ];

        $this->assertEquals($wheres, $b->getOrWheres());
    }

    public function testWhereInvalidOperator()
    {
        $b = new Builder();

        $this->setExpectedException('Adldap\Exceptions\InvalidQueryOperatorException');

        $b->where('field', 'invalid', 'value');
    }

    public function testOrWhereInvalidOperator()
    {
        $b = new Builder();

        $this->setExpectedException('Adldap\Exceptions\InvalidQueryOperatorException');

        $b->orWhere('field', 'invalid', 'value');
    }
}
