<?php

namespace Adldap\Tests\Query\Bindings;

use Adldap\Tests\TestCase;
use Adldap\Query\Bindings\Filter;

class FilterTest extends TestCase
{
    public function test_construct()
    {
        $filter = new Filter('(cn=test)');

        $this->assertEquals('(cn=test)', $filter->getQuery());
        $this->assertEquals('(cn=test)', (string) $filter);
    }
}
