<?php

namespace Adldap\tests\Query;

use Adldap\Query\Builder;
use Adldap\Query\Grammar;
use Adldap\Tests\UnitTestCase;

class BuilderTest extends UnitTestCase
{
    protected function newBuilder()
    {
        $connection = $this->mock('Adldap\Connections\ConnectionInterface');

        return new Builder($connection, new Grammar());
    }

    public function testConstruct()
    {
        $b = $this->newBuilder();

        $this->assertEmpty($b->getQuery());
    }

    public function testSelectArray()
    {
        $b = $this->newBuilder();

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
        $b = $this->newBuilder();

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
        $b = $this->newBuilder();

        $b->select('');

        $expected = [];

        $this->assertEquals($expected, $b->getSelects());
    }

    public function testHasSelects()
    {
        $b = $this->newBuilder();

        $b->select('test');

        $this->assertTrue($b->hasSelects());
    }

    public function testWhere()
    {
        $b = $this->newBuilder();

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
        $b = $this->newBuilder();

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
        $b = $this->newBuilder();

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
        $b = $this->newBuilder();

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
        $b = $this->newBuilder();

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
        $b = $this->newBuilder();

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
        $b = $this->newBuilder();

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
        $b = $this->newBuilder();

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
        $b = $this->newBuilder();

        $this->setExpectedException('Adldap\Exceptions\InvalidQueryOperatorException');

        $b->where('field', 'invalid', 'value');
    }

    public function testOrWhereInvalidOperator()
    {
        $b = $this->newBuilder();

        $this->setExpectedException('Adldap\Exceptions\InvalidQueryOperatorException');

        $b->orWhere('field', 'invalid', 'value');
    }

    public function testBuiltWhere()
    {
        $b = $this->newBuilder();

        $b->where('field', '=' , 'value');

        $expected = '(field=\76\61\6c\75\65)';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function testBuiltWheres()
    {
        $b = $this->newBuilder();

        $b->where('field', '=' , 'value');

        $b->where('other', '=', 'value');

        $expected = '(&(field=\76\61\6c\75\65)(other=\76\61\6c\75\65))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function testBuiltWhereStartsWith()
    {
        $b = $this->newBuilder();

        $b->whereStartsWith('field', 'value');

        $expected = '(field=\76\61\6c\75\65*)';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function testBuiltWhereEndsWith()
    {
        $b = $this->newBuilder();

        $b->whereEndsWith('field', 'value');

        $expected = '(field=*\76\61\6c\75\65)';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function testBuiltWhereContains()
    {
        $b = $this->newBuilder();

        $b->whereContains('field', 'value');

        $expected = '(field=*\76\61\6c\75\65*)';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function testBuiltOrWhere()
    {
        $b = $this->newBuilder();

        $b->orWhere('field', '=' , 'value');

        $expected = '(&(|(field=\76\61\6c\75\65)))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function testBuiltOrWheres()
    {
        $b = $this->newBuilder();

        $b->orWhere('field', '=' , 'value');

        $b->orWhere('other', '=', 'value');

        $expected = '(&(|(field=\76\61\6c\75\65)(other=\76\61\6c\75\65)))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function testBuiltOrWhereStartsWith()
    {
        $b = $this->newBuilder();

        $b->orWhereStartsWith('field', 'value');

        $expected = '(&(|(field=\76\61\6c\75\65*)))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function testBuiltOrWhereEndsWith()
    {
        $b = $this->newBuilder();

        $b->orWhereEndsWith('field', 'value');

        $expected = '(&(|(field=*\76\61\6c\75\65)))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function testBuiltOrWhereContains()
    {
        $b = $this->newBuilder();

        $b->orWhereContains('field', 'value');

        $expected = '(&(|(field=*\76\61\6c\75\65*)))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function testBuiltWhereAndOrWheres()
    {
        $b = $this->newBuilder();

        $b->where('field', '=', 'value');

        $b->orWhere('or', '=', 'value');

        $expected = '(&(field=\76\61\6c\75\65)(|(or=\76\61\6c\75\65)))';

        $this->assertEquals($expected, $b->getQuery());
    }
}
