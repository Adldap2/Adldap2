<?php

namespace Adldap\tests\Query;

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

        $this->assertEquals($expected, $b->getSelects());
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

        $this->assertEquals($expected, $b->getSelects());
    }

    public function testSelectEmptyString()
    {
        $b = new Builder();

        $b->select('');

        $this->assertEquals([], $b->getSelects());
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
                'field'    => 'cn',
                'operator' => '=',
                'value'    => '\74\65\73\74',
            ],
        ];

        $this->assertEquals($wheres, $b->getWheres());
    }

    public function testWhereContains()
    {
        $b = new Builder();

        $b->whereContains('cn', 'test');

        $wheres = [
            [
                'field'    => 'cn',
                'operator' => 'contains',
                'value'    => '\74\65\73\74',
            ],
        ];

        $this->assertEquals($wheres, $b->getWheres());
    }

    public function testWhereStartsWith()
    {
        $b = new Builder();

        $b->whereStartsWith('cn', 'test');

        $wheres = [
            [
                'field'    => 'cn',
                'operator' => 'starts_with',
                'value'    => '\74\65\73\74',
            ],
        ];

        $this->assertEquals($wheres, $b->getWheres());
    }

    public function testWhereEndsWith()
    {
        $b = new Builder();

        $b->whereEndsWith('cn', 'test');

        $wheres = [
            [
                'field'    => 'cn',
                'operator' => 'ends_with',
                'value'    => '\74\65\73\74',
            ],
        ];

        $this->assertEquals($wheres, $b->getWheres());
    }

    public function testOrWhere()
    {
        $b = new Builder();

        $b->orWhere('cn', '=', 'test');

        $wheres = [
            [
                'field'    => 'cn',
                'operator' => '=',
                'value'    => '\74\65\73\74',
            ],
        ];

        $this->assertEquals($wheres, $b->getOrWheres());
    }

    public function testOrWhereContains()
    {
        $b = new Builder();

        $b->orWhereContains('cn', 'test');

        $wheres = [
            [
                'field'    => 'cn',
                'operator' => 'contains',
                'value'    => '\74\65\73\74',
            ],
        ];

        $this->assertEquals($wheres, $b->getOrWheres());
    }

    public function testOrWhereStartsWith()
    {
        $b = new Builder();

        $b->orWhereStartsWith('cn', 'test');

        $wheres = [
            [
                'field'    => 'cn',
                'operator' => 'starts_with',
                'value'    => '\74\65\73\74',
            ],
        ];

        $this->assertEquals($wheres, $b->getOrWheres());
    }

    public function testOrWhereEndsWith()
    {
        $b = new Builder();

        $b->orWhereEndsWith('cn', 'test');

        $wheres = [
            [
                'field'    => 'cn',
                'operator' => 'ends_with',
                'value'    => '\74\65\73\74',
            ],
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

    public function testBuiltWhere()
    {
        $b = new Builder();

        $b->where('field', '=' , 'value');

        $expected = '(field=\76\61\6c\75\65)';

        $this->assertEquals($expected, $b->get());
    }

    public function testBuiltWheres()
    {
        $b = new Builder();

        $b->where('field', '=' , 'value');

        $b->where('other', '=', 'value');

        $expected = '(&(field=\76\61\6c\75\65)(other=\76\61\6c\75\65))';

        $this->assertEquals($expected, $b->get());
    }

    public function testBuiltWhereStartsWith()
    {
        $b = new Builder();

        $b->whereStartsWith('field', 'value');

        $expected = '(field=\76\61\6c\75\65*)';

        $this->assertEquals($expected, $b->get());
    }

    public function testBuiltWhereEndsWith()
    {
        $b = new Builder();

        $b->whereEndsWith('field', 'value');

        $expected = '(field=*\76\61\6c\75\65)';

        $this->assertEquals($expected, $b->get());
    }

    public function testBuiltWhereContains()
    {
        $b = new Builder();

        $b->whereContains('field', 'value');

        $expected = '(field=*\76\61\6c\75\65*)';

        $this->assertEquals($expected, $b->get());
    }

    public function testBuiltOrWhere()
    {
        $b = new Builder();

        $b->orWhere('field', '=' , 'value');

        $expected = '(&(|(field=\76\61\6c\75\65)))';

        $this->assertEquals($expected, $b->get());
    }

    public function testBuiltOrWheres()
    {
        $b = new Builder();

        $b->orWhere('field', '=' , 'value');

        $b->orWhere('other', '=', 'value');

        $expected = '(&(|(field=\76\61\6c\75\65)(other=\76\61\6c\75\65)))';

        $this->assertEquals($expected, $b->get());
    }

    public function testBuiltOrWhereStartsWith()
    {
        $b = new Builder();

        $b->orWhereStartsWith('field', 'value');

        $expected = '(&(|(field=\76\61\6c\75\65*)))';

        $this->assertEquals($expected, $b->get());
    }

    public function testBuiltOrWhereEndsWith()
    {
        $b = new Builder();

        $b->orWhereEndsWith('field', 'value');

        $expected = '(&(|(field=*\76\61\6c\75\65)))';

        $this->assertEquals($expected, $b->get());
    }

    public function testBuiltOrWhereContains()
    {
        $b = new Builder();

        $b->orWhereContains('field', 'value');

        $expected = '(&(|(field=*\76\61\6c\75\65*)))';

        $this->assertEquals($expected, $b->get());
    }

    public function testBuiltWhereAndOrWheres()
    {
        $b = new Builder();

        $b->where('field', '=', 'value');

        $b->orWhere('or', '=', 'value');

        $expected = '(&(field=\76\61\6c\75\65)(|(or=\76\61\6c\75\65)))';

        $this->assertEquals($expected, $b->get());
    }

    public function testWrap()
    {
        $b = new Builder();

        $wrapped = $b->wrap('test');

        $expected = '(test)';

        $this->assertEquals($expected, $wrapped);
    }
}
