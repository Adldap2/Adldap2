<?php

namespace Adldap\Tests\Query\Bindings;

use Adldap\Query\Bindings\Filter;
use Adldap\Tests\UnitTestCase;

class FilterTest extends UnitTestCase
{
    public function testConstruct()
    {
        $filter = new Filter('(cn=test)');

        $this->assertEquals('(cn=test)', $filter->getQuery());
        $this->assertEquals('(cn=test)', (string) $filter);
    }
}
