<?php

namespace Adldap\Tests\Query;

use Adldap\Query\Grammar;
use Adldap\Tests\TestCase;

class GrammarTest extends TestCase
{
    public function newGrammar()
    {
        return new Grammar();
    }

    public function test_wrap()
    {
        $g = $this->newGrammar();

        $wrapped = $g->wrap('test');

        $expected = '(test)';

        $this->assertEquals($expected, $wrapped);
    }

    public function test_wrap_prefix()
    {
        $g = $this->newGrammar();

        $wrapped = $g->wrap('test', '(!');

        $expected = '(!test)';

        $this->assertEquals($expected, $wrapped);
    }

    public function test_wrap_suffix()
    {
        $g = $this->newGrammar();

        $wrapped = $g->wrap('test', null, '=)');

        $expected = 'test=)';

        $this->assertEquals($expected, $wrapped);
    }

    public function test_wrap_both()
    {
        $g = $this->newGrammar();

        $wrapped = $g->wrap('test', '(!prefix', 'suffix)');

        $expected = '(!prefixtestsuffix)';

        $this->assertEquals($expected, $wrapped);
    }
}
