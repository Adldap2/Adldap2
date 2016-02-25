<?php

namespace Adldap\tests\Query;

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

        return new Builder($connection, new Grammar());
    }

    protected function newConnectionMock()
    {
        return $this->mock('Adldap\Connections\ConnectionInterface');
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
            'objectclass',
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
            'objectclass',
            'dn',
        ];

        $this->assertEquals($expected, $b->getSelects());
    }

    public function testSelectEmptyString()
    {
        $b = $this->newBuilder();

        $b->select('');

        $expected = [
            '',
            'objectcategory',
            'objectclass',
            'dn'
        ];

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

    public function testWhereWithArray()
    {
        $b = $this->newBuilder();

        $b->where([
            'cn'    => 'test',
            'name'  => 'test',
        ]);

        $wheres = [
            [
                'field'    => 'cn',
                'operator' => '=',
                'value'    => '\74\65\73\74',
            ],
            [
                'field'    => 'name',
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

    public function testOrWhereWithArray()
    {
        $b = $this->newBuilder();

        $b->orWhere([
            'cn'    => 'test',
            'name'  => 'test',
        ]);

        $wheres = [
            [
                'field'    => 'cn',
                'operator' => '=',
                'value'    => '\74\65\73\74',
            ],
            [
                'field'    => 'name',
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

        $expected = '(field=\76\61\6c\75\65)';

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
        $b->orWhereStartsWith('other-field', 'value');

        $expected = '(&(|(field=\76\61\6c\75\65*)(other-field=\76\61\6c\75\65*)))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function testBuiltOrWhereEndsWith()
    {
        $b = $this->newBuilder();

        $b->orWhereEndsWith('field', 'value');
        $b->orWhereEndsWith('other-field', 'value');

        $expected = '(&(|(field=*\76\61\6c\75\65)(other-field=*\76\61\6c\75\65)))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function testBuiltOrWhereContains()
    {
        $b = $this->newBuilder();

        $b->orWhereContains('field', 'value');
        $b->orWhereContains('other-field', 'value');

        $expected = '(&(|(field=*\76\61\6c\75\65*)(other-field=*\76\61\6c\75\65*)))';

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

    public function testBuiltWhereHas()
    {
        $b = $this->newBuilder();

        $b->whereHas('field');

        $expected = '(field=*)';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function testBuiltWhereNotHas()
    {
        $b = $this->newBuilder();

        $b->whereNotHas('field');

        $expected = '(!(field=*))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function testBuiltWhereNotContains()
    {
        $b = $this->newBuilder();

        $b->whereNotContains('field', 'value');

        $expected = '(!(field=*\76\61\6c\75\65*))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function testBuiltWhereApproximatelyEquals()
    {
        $b = $this->newBuilder();

        $b->whereApproximatelyEquals('field', 'value');

        $expected = '(field~=\76\61\6c\75\65)';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function testBuiltOrWhereHas()
    {
        $b = $this->newBuilder();

        $b->orWhereHas('field');
        $b->orWhereHas('other-field');

        $expected = '(&(|(field=*)(other-field=*)))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function testBuiltOrWheresWithWheres()
    {
        $b = $this->newBuilder();

        $b->whereHas('field');
        $b->orWhereHas('field');
        $b->orWhereHas('other-field');

        $expected = '(&(field=*)(|(field=*)(other-field=*)))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function testBuiltOrWhereNotHas()
    {
        $b = $this->newBuilder();

        $b->orWhereNotHas('field');
        $b->orWhereNotHas('other-field');

        $expected = '(&(|(!(field=*))(!(other-field=*))))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function testBuiltOrWhereApproximatelyEquals()
    {
        $b = $this->newBuilder();

        $b->orWhereApproximatelyEquals('field', 'value');
        $b->orWhereApproximatelyEquals('other-field', 'value');

        $expected = '(&(|(field~=\76\61\6c\75\65)(other-field~=\76\61\6c\75\65)))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function testBuiltRawFilter()
    {
        $b = $this->newBuilder();

        $filter = '(field=value)';

        $b->rawFilter($filter);

        $this->assertEquals($filter, $b->getQuery());
    }

    public function testBuiltRawFilterWithWheres()
    {
        $b = $this->newBuilder();

        $b->rawFilter('(field=value)');

        $b->where('field', '=', 'value');

        $b->orWhere('field', '=', 'value');

        $expected = '(&(field=value)(field=\76\61\6c\75\65)(|(field=\76\61\6c\75\65)))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function testBuiltRawFilterMultiple()
    {
        $b = $this->newBuilder();

        $b->rawFilter('(field=value)');

        $b->rawFilter('(|(field=value))');

        $b->rawFilter('(field=value)');

        $expected = '(&(field=value)(|(field=value))(field=value))';

        $this->assertEquals($expected, $b->getQuery());
    }

    public function testNewCollection()
    {
        $b = $this->newBuilder();

        $elements = ['e1', 'e2', 'e3'];

        $collection = $b->newCollection($elements);

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $collection);
        $this->assertEquals($elements, $collection->toArray());
    }

    public function testPaginateWithNoResults()
    {
        $connection = $this->newConnectionMock();

        $connection->shouldReceive('controlPagedResult')->once()->withArgs([50, true, '']);
        $connection->shouldReceive('search')->once()->withArgs(['', '(field=\76\61\6c\75\65)', []])->andReturn(null);

        $b = $this->newBuilder($connection);

        $this->assertFalse($b->where('field', '=', 'value')->paginate(50));
    }

    public function testPaginateWithResults()
    {
        $connection = $this->newConnectionMock();

        $rawEntries = [
            'count' => 1,
            [
                'cn' => ['Test'],
                'dn' => 'cn=Test,dc=corp,dc=acme,dc=org',
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
            $this->assertEquals($rawEntries[0]['dn'], $model->getDn());
        }
    }

    public function testAddBinding()
    {
        $b = $this->newBuilder();

        $b->addBinding('cn', '=', 'test', 'where');
        $b->addBinding('cn', '=', 'test', 'orWhere');

        $this->assertEquals(1, count($b->wheres));
        $this->assertEquals(1, count($b->orWheres));

        $where = [
            'field' => 'cn',
            'operator' => '=',
            'value' => '\74\65\73\74',
        ];

        $this->assertEquals($where, $b->wheres[0]);
        $this->assertEquals($where, $b->orWheres[0]);
    }

    public function testAddBindingInvalidArgumentException()
    {
        $b = $this->newBuilder();

        $this->setExpectedException('InvalidArgumentException');

        $b->addBinding('cn', '=', 'test', 'invalid binding');
    }
}
