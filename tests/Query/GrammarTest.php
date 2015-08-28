<?php

namespace Adldap\tests\Query;

use Adldap\Query\Grammar;
use Adldap\Tests\UnitTestCase;

class GrammarTest extends UnitTestCase
{
    public function newGrammar()
    {
        return new Grammar();
    }

    public function testWrap()
    {
        $g = $this->newGrammar();

        $wrapped = $g->wrap('test');

        $expected = '(test)';

        $this->assertEquals($expected, $wrapped);
    }

    public function testWrapPrefix()
    {
        $g = $this->newGrammar();

        $wrapped = $g->wrap('test', '(!');

        $expected = '(!test)';

        $this->assertEquals($expected, $wrapped);
    }

    public function testWrapSuffix()
    {
        $g = $this->newGrammar();

        $wrapped = $g->wrap('test', null, '=)');

        $expected = 'test=)';

        $this->assertEquals($expected, $wrapped);
    }

    public function testWrapBoth()
    {
        $g = $this->newGrammar();

        $wrapped = $g->wrap('test', '(!prefix', 'suffix)');

        $expected = '(!prefixtestsuffix)';

        $this->assertEquals($expected, $wrapped);
    }
}
