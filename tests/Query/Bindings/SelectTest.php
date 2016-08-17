<?php

namespace Adldap\Tests\Query\Bindings;

use Adldap\Tests\TestCase;
use Adldap\Query\Bindings\Select;

class SelectTest extends TestCase
{
    public function test_construct()
    {
        $select = new Select('test');

        $this->assertEquals('test', $select->getField());
        $this->assertEquals('test', (string) $select);
    }
}
