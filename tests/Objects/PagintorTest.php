<?php

namespace Adldap\Tests\Objects;

use Adldap\Objects\Paginator;
use Adldap\Tests\FunctionalTestCase;

class PagintorTest extends FunctionalTestCase
{
    public function testPaginatorConstruct()
    {
        $paginator = new Paginator([], 50, 0, 0);

        $this->assertEquals(0, $paginator->getPages());
        $this->assertEquals(0, $paginator->count());
        $this->assertEquals(50, $paginator->getPerPage());
    }
    
    public function testPaginatorPages()
    {
        $data = [
            [
                'name' => 'John',
            ],
            [
                'name' => 'Suzy',
            ]
        ];

        $paginator = new Paginator($data, 1, 0, 2);

        $this->assertEquals($data, $paginator->getAttributes());
        $this->assertEquals(2, $paginator->count());
        $this->assertEquals(0, $paginator->getCurrentPage());

        // Tests that only the first entry is shown
        foreach($paginator as $entry)
        {
            $this->assertEquals(['name' => 'John'], $entry);
        }

        $paginator2 = new Paginator($data, 1, 1, 2);

        $this->assertEquals(1, $paginator2->getCurrentPage());

        foreach($paginator2 as $entry)
        {
            $this->assertEquals(['name' => 'Suzy'], $entry);
        }
    }
}