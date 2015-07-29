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

        $selects = [
            'testing',
            'objectclass',
        ];

        $this->assertEquals($selects,  $b->getSelects());
    }

    public function testSelectString()
    {
        $b = new Builder();

        $b->select('testing');

        $selects = [
            'testing',
            'objectclass',
        ];

        $this->assertEquals($selects,  $b->getSelects());
    }

    public function testSelectEmptyString()
    {
        $b = new Builder();

        $b->select('');

        $this->assertEquals([],  $b->getSelects());
    }
}
