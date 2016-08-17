<?php

namespace Adldap\Tests\Query;

use Adldap\Query\Operator;
use Adldap\Tests\TestCase;

class OperatorTest extends TestCase
{
    public function test_all()
    {
        $operators = Operator::all();

        $expected = [
            'has'                   => '*',
            'notHas'                => '!*',
            'equals'                => '=',
            'doesNotEqual'          => '!',
            'greaterThanOrEquals'   => '>=',
            'lessThanOrEquals'      => '<=',
            'approximatelyEquals'   => '~=',
            'startsWith'            => 'starts_with',
            'notStartsWith'         => 'not_starts_with',
            'endsWith'              => 'ends_with',
            'notEndsWith'           => 'not_ends_with',
            'contains'              => 'contains',
            'notContains'           => 'not_contains',
        ];

        $this->assertEquals($expected, $operators);
    }
}
