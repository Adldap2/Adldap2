<?php

namespace Adldap\Tests\Query\Bindings;

use Adldap\Query\Bindings\Filter;
use Adldap\Tests\TestCase;

class FilterTest extends TestCase
{
    public function testConstruct()
    {
        $filter = new Filter('(cn=test)');

        $this->assertEquals('(cn=test)', $filter->getQuery());
        $this->assertEquals('(cn=test)', (string) $filter);
    }
}
