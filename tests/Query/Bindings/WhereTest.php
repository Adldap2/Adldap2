<?php

namespace Adldap\Tests\Query\Bindings;

use Adldap\Query\Bindings\Where;
use Adldap\Tests\UnitTestCase;

class WhereTest extends UnitTestCase
{
    public function testConstruct()
    {
        $where = new Where('\\cn\\', '=', 'test');

        $this->assertEquals('\5ccn\5c', $where->getField());
        $this->assertEquals('=', $where->getOperator());
        $this->assertEquals('\74\65\73\74', $where->getValue());
    }

    public function testConstructInvalidOperatorException()
    {
        $this->setExpectedException('Adldap\Exceptions\InvalidQueryOperatorException');

        new Where('cn', 'invalid', 'test');
    }
}
