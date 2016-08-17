<?php

namespace Adldap\Tests\Query\Bindings;

use Adldap\Tests\TestCase;
use Adldap\Query\Bindings\Where;

class WhereTest extends TestCase
{
    public function test_construct()
    {
        $where = new Where('\\cn\\', '=', 'test');

        $this->assertEquals('\5ccn\5c', $where->getField());
        $this->assertEquals('=', $where->getOperator());
        $this->assertEquals('\74\65\73\74', $where->getValue());
    }

    public function test_construct_invalid_operator_exception()
    {
        $this->setExpectedException('Adldap\Exceptions\InvalidQueryOperatorException');

        new Where('cn', 'invalid', 'test');
    }
}
