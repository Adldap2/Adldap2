<?php

namespace Adldap\Tests\Query\Bindings;

use Adldap\Query\Bindings\Select;
use Adldap\Tests\TestCase;

class SelectTest extends TestCase
{
    public function testConstruct()
    {
        $select = new Select('test');

        $this->assertEquals('test', $select->getField());
        $this->assertEquals('test', (string) $select);
    }
}
